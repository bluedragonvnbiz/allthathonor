<?= $formHtml ?>
<form class="card">
	<div class="card-header d-flex align-items-center justify-content-between">
		<strong class="title">혜택 정보</strong>
		<button class="btn btn-primary lh-1" type="submit">저장</button>
	</div>
	<div class="card-body p-0">
		<div class="section-box">
			<nav class="nav-tab-btn d-flex align-items-center">
				<?php $isFirst = true; ?>
				<?php foreach ($categories as $categoryKey => $category): ?>
					<button class="btn p-0 border-0 <?= $isFirst ? 'active' : '' ?>" type="button" data-category="<?= $categoryKey ?>">
						<?= $category['name'] ?>
					</button>
					<?php $isFirst = false; ?>
				<?php endforeach; ?>
			</nav>
		</div>
		
		<?php foreach ($categories as $categoryKey => $category): ?>
		<div class="section-box category-content" id="<?= $categoryKey ?>" style="<?= $categoryKey !== 'travel_care' ? 'display: none;' : '' ?>">			
			<table class="table table-hover mb-0 table-input">
				<thead>
					<tr>
						<th style="width: 180px;">제공혜택</th>
						<th>설명</th>
						<th style="width: 80px;">사용여부</th> 
						<th style="width: 80px;">
							<div class="d-flex align-items-center justify-content-center">
								<span>요약혜택</span>
								<div class="pv-tooltip">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<circle cx="10.0003" cy="10.0003" r="7.5" stroke="#A0A5B5" stroke-width="1.66667"/>
										<circle cx="9.99967" cy="13.1252" r="1.04167" fill="#A0A5B5"/>
										<path d="M7.91699 7.91691C7.91699 6.99644 8.84973 6.25024 10.0003 6.25024C11.1509 6.25024 12.0837 6.99644 12.0837 7.91691C12.0837 8.72371 11.3671 9.39662 10.4153 9.55051C10.1881 9.58725 10.0003 9.77013 10.0003 10.0002V10.4169" stroke="#A0A5B5" stroke-width="1.66667" stroke-linecap="round"/>
									</svg>
									<div class="tooltip-content">
										멤버십마다 사용 중인 혜택중에서 최대<br>3개의 요약혜택만 선택할 수 있으며, <br>이 3개의 요약혜택은 멤버십<br> 페이지의 섹션 1에 표시됩니다.
									</div>
								</div>
							</div>
						</th>				      
					</tr>
				</thead>
				<tbody>
				<?php if (!empty($vouchers)): ?>
					<?php foreach ($vouchers as $voucher): ?>
						<?php 
						// Check if this voucher is already selected for this category
						$categoryField = $categoryKey . '_vouchers';
						$isSelected = false;
						$isSummaryBenefit = false;
						
						if (isset($card_info[$categoryField]) && is_array($card_info[$categoryField])) {
							foreach ($card_info[$categoryField] as $voucherData) {
								if ($voucherData['id'] == $voucher['id']) {
									$isSelected = true;
									$isSummaryBenefit = $voucherData['is_summary'] ?? false;
									break;
								}
							}
						}
						?>
						<tr>
							<td><?= esc_html($voucher['name']) ?></td>
							<td style="text-align:left;"><?= esc_html($voucher['short_description']) ?></td>
							<td>
								<div class="form-check checkbox justify-content-center">
									<input class="form-check-input" type="checkbox" value="<?= $voucher['id'] ?>" name="<?= $categoryKey ?>_vouchers[]" data-category="<?= $categoryKey ?>" <?= $isSelected ? 'checked' : '' ?>>
								</div>
							</td>
							<td>
								<div class="form-check checkbox justify-content-center">
									<input class="form-check-input summary-benefit" type="checkbox" value="<?= $voucher['id'] ?>" name="summary_benefits[]" data-category="<?= $categoryKey ?>" data-max="3" <?= $isSummaryBenefit ? 'checked' : '' ?> <?= !$isSelected ? 'disabled' : '' ?>>
								</div>
							</td>
						</tr>
					<?php endforeach; ?>
				<?php else: ?>
					<tr>
						<td colspan="4" class="text-center text-muted">해당 카테고리에 등록된 혜택이 없습니다.</td>
					</tr>
				<?php endif; ?>
				</tbody>
			</table>			
		</div>
		<?php endforeach; ?>
		
		<?php foreach ($categories as $categoryKey => $category): ?>
		<div class="section-box border-0 category-usage-guide" id="<?= $categoryKey ?>_usage_guide" style="<?= $categoryKey !== 'travel_care' ? 'display: none;' : '' ?>">
			<div class="d-flex column-gap-30">
				<label class="form-label">이용안내</label>
				<textarea class="form-control" name="<?= $categoryKey ?>_usage_guide" placeholder="<?= $category['name'] ?> 이용안내를 입력해주세요." style="height: 126px;"><?= htmlspecialchars(trim($card_info[$categoryKey . '_usage_guide'] ?? '')) ?></textarea>
			</div>
		</div>
		<?php endforeach; ?>
	</div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching functionality
    const tabButtons = document.querySelectorAll('.nav-tab-btn button');
    const categoryContents = document.querySelectorAll('.category-content');
    const categoryUsageGuides = document.querySelectorAll('.category-usage-guide');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const category = this.getAttribute('data-category');
            
            // Remove active class from all buttons
            tabButtons.forEach(btn => btn.classList.remove('active'));
            // Add active class to clicked button
            this.classList.add('active');
            
            // Hide all content sections
            categoryContents.forEach(content => content.style.display = 'none');
            // Show selected content
            document.getElementById(category).style.display = 'block';
            
            // Hide all usage guide sections
            categoryUsageGuides.forEach(guide => guide.style.display = 'none');
            // Show selected usage guide
            document.getElementById(category + '_usage_guide').style.display = 'block';
        });
    });
    
    // Handle voucher selection and summary checkbox enable/disable
    const voucherCheckboxes = document.querySelectorAll('input[name$="_vouchers[]"]');
    voucherCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const category = this.getAttribute('data-category');
            const voucherId = this.value;
            const summaryCheckbox = document.querySelector(`.summary-benefit[value="${voucherId}"][data-category="${category}"]`);
            
            if (summaryCheckbox) {
                summaryCheckbox.disabled = !this.checked;
                if (!this.checked) {
                    summaryCheckbox.checked = false;
                }
            }
        });
    });
    
    // Summary benefits limit (max 3 per category)
    const summaryCheckboxes = document.querySelectorAll('.summary-benefit');
    summaryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const category = this.getAttribute('data-category');
            const checkedSummaryInCategory = document.querySelectorAll(`.summary-benefit[data-category="${category}"]:checked`);
            
            if (checkedSummaryInCategory.length > 3) {
                this.checked = false;
                showToast('요약혜택은 최대 3개까지 선택할 수 있습니다');
            }
        });
    });
    
    // Toast popup function
    function showToast(message) {
        // Remove existing toast if any
        const existingToast = document.querySelector('.toast-popup');
        if (existingToast) {
            existingToast.remove();
        }
        
        // Create toast element
        const toast = document.createElement('div');
        toast.className = 'toast-popup';
        toast.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #000;
            color: #fff;
            padding: 12px 20px;
            border-radius: 6px;
            z-index: 9999;
            font-size: 14px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            animation: slideIn 0.3s ease;
        `;
        toast.textContent = message;
        
        // Add animation CSS
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
        
        // Add to page
        document.body.appendChild(toast);
        
        // Auto remove after 3 seconds
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
        
        // Add slideOut animation
        style.textContent += `
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
    }
});
</script>