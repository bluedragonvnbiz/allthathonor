/**
 * Search Field Handler
 */
jQuery(document).ready(function($) {
    
    let searchTimeout;
    let currentSearchType = '';
    
    // Handle search input
    $(document).on('input', '.search-input', function() {
        const $input = $(this);
        const searchType = $input.attr('search-type');
        const searchTerm = $input.val().trim();
        const $container = $input.closest('.search-container');
        const $results = $container.find('.search-results');
        
        // Store current hidden ID and input value when user starts typing
        if (!$(this).data('current-hidden-id')) {
            $(this).data('current-hidden-id', $container.find('input[type="hidden"]').val());
            $(this).data('current-input-value', $input.val());
        }
        
        // Mark that user is searching
        $(this).data('is-searching', true);
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        // Hide results if no search term
        if (searchTerm.length < 2) {
            $results.hide();
            return;
        }
        
        // Set timeout for search (debounce)
        searchTimeout = setTimeout(function() {
            performSearch(searchType, searchTerm, $container);
        }, 300);
    });
    
    // Handle search result selection
    $(document).on('click', '.search-result-item', function() {
        const $item = $(this);
        const $container = $item.closest('.search-container');
        const $input = $container.find('.search-input');
        const $hiddenId = $container.find('input[type="hidden"]');
        
        // Get voucher name from the selected item
        const voucherName = $item.find('.search-result-title').text();
        const voucherId = $item.data('id');
        
        // Set input value to voucher name and hidden ID
        $input.val(voucherName);
        $hiddenId.val(voucherId);
        
        // Clear current data since user made a selection
        $input.removeData('current-hidden-id current-input-value is-searching');
        
        // Hide results
        $container.find('.search-results').hide();
        
        // Trigger change event
        $input.trigger('change');
        
        console.log('Selected voucher:', voucherName, 'ID:', voucherId);
    });
    
    // Hide results when clicking outside or moving mouse outside
    $(document).on('click mouseleave', function(e) {
        if (!$(e.target).closest('.search-container').length) {
            $('.search-results').each(function() {
                const $results = $(this);
                const $container = $results.closest('.search-container');
                const $input = $container.find('.search-input');
                const $hiddenId = $container.find('input[type="hidden"]');
                
                // Restore original title from hidden input
                if ($input.data('is-searching')) {
                    const currentHiddenId = $input.data('current-hidden-id');
                    console.log('Restoring voucher, currentHiddenId:', currentHiddenId);
                    
                    // Always restore the title from hidden input if there's an ID
                    const $hiddenInput = $container.find('input[type="hidden"]');
                    const originalTitle = $hiddenInput.attr('title');
                    if (originalTitle) {
                        console.log('Restoring title from hidden input:', originalTitle);
                        $input.val(originalTitle);
                    }
                    
                    $input.removeData('current-hidden-id current-input-value is-searching');
                }
                
                $results.hide();
            });
        }
    });
    
    // Also handle when search results lose focus
    $(document).on('focusout', '.search-results', function() {
        const $results = $(this);
        const $container = $results.closest('.search-container');
        const $input = $container.find('.search-input');
        const $hiddenId = $container.find('input[type="hidden"]');
        
        // Restore original title from hidden input
        if ($input.data('is-searching')) {
            const currentHiddenId = $input.data('current-hidden-id');
            
            // Always restore the title from hidden input if there's an ID
            const $hiddenInput = $container.find('input[type="hidden"]');
            const originalTitle = $hiddenInput.attr('title');
            if (originalTitle) {
                console.log('Restoring title from hidden input (focusout):', originalTitle);
                $input.val(originalTitle);
            }
            
            $input.removeData('current-hidden-id current-input-value is-searching');
        }
        
        $results.hide();
    });
    
    // Handle keyboard navigation
    $(document).on('keydown', '.search-input', function(e) {
        const $container = $(this).closest('.search-container');
        const $results = $container.find('.search-results');
        const $items = $results.find('.search-result-item');
        const $selected = $results.find('.search-result-item.selected');
        
        if (!$results.is(':visible') || $items.length === 0) return;
        
        switch (e.keyCode) {
            case 38: // Up arrow
                e.preventDefault();
                if ($selected.length === 0) {
                    $items.last().addClass('selected');
                } else {
                    $selected.removeClass('selected')
                        .prev('.search-result-item')
                        .addClass('selected');
                }
                break;
                
            case 40: // Down arrow
                e.preventDefault();
                if ($selected.length === 0) {
                    $items.first().addClass('selected');
                } else {
                    $selected.removeClass('selected')
                        .next('.search-result-item')
                        .addClass('selected');
                }
                break;
                
            case 13: // Enter
                e.preventDefault();
                $selected.click();
                break;
                
            case 27: // Escape
                $results.hide();
                break;
        }
    });
    
    /**
     * Perform AJAX search
     */
    function performSearch(searchType, searchTerm, $container) {
        const $results = $container.find('.search-results');
        
        // Show loading state
        $results.html('<div class="search-result-item">검색 중...</div>').show();
        
        // Make AJAX request
        $.ajax({
            url: define.ajax_url,
            type: 'POST',
            data: {
                action: 'search_data',
                search_type: searchType,
                search_term: searchTerm,
                nonce: $('input[name="nonce"]').first().val()
            },
            success: function(response) {
                if (response.success && response.data.results) {
                    displaySearchResults(response.data.results, $container);
                } else {
                    $results.html('<div class="search-result-item">결과가 없습니다.</div>').show();
                }
            },
            error: function() {
                $results.html('<div class="search-result-item">검색 중 오류가 발생했습니다.</div>').show();
            }
        });
    }
    
    /**
     * Display search results
     */
    function displaySearchResults(results, $container) {
        const $results = $container.find('.search-results');
        
        if (results.length === 0) {
            $results.html('<div class="search-result-item">결과가 없습니다.</div>').show();
            return;
        }
        
        let html = '';
        results.forEach(function(result) {
            html += '<div class="search-result-item" data-id="' + result.id + '">';
            html += '<div class="search-result-title">' + result.name + '</div>';
            if (result.short_description) {
                html += '<div class="search-result-description">' + result.short_description + '</div>';
            }
            html += '</div>';
        });
        
        $results.html(html).show();
    }
});
