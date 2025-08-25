<form class="card" method="GET" action="">
	<div class="card-body p-0">
		<div class="section-box p-4 border-0">
			<div class="d-flex gap-40 align-items-center w-100">
				<label class="form-label">검색</label>
				<div class="d-flex column-gap-2 w-100">
					<select class="form-select" name="search_type">
						<option value="corporate_name" <?= $searchType === 'corporate_name' ? 'selected' : '' ?>>법인명</option>
						<option value="contact_person" <?= $searchType === 'contact_person' ? 'selected' : '' ?>>담당자명</option>
						<option value="contact_phone" <?= $searchType === 'contact_phone' ? 'selected' : '' ?>>연락처</option>
						<option value="email" <?= $searchType === 'email' ? 'selected' : '' ?>>이메일</option>
						<option value="inquiry_number" <?= $searchType === 'inquiry_number' ? 'selected' : '' ?>>문의번호</option>
					</select>
					<input type="text" class="form-control" name="search_keyword" placeholder="검색어를 입력하세요." value="<?= htmlspecialchars($searchKeyword) ?>">
				</div>
			</div>
			<div class="d-flex gap-40 align-items-center">
				<label class="form-label">카테고리</label>
				<div class="d-flex column-gap-2 position-relative">
					<select class="form-select" name="category_main" id="category_main">
						<option value="">메인 카테고리 선택</option>
						<?php foreach ($mainCategories as $category): ?>
							<option value="<?= htmlspecialchars($category) ?>" <?= $categoryMain === $category ? 'selected' : '' ?>><?= htmlspecialchars($category) ?></option>
						<?php endforeach; ?>
					</select>
					<select class="form-select" name="category_sub" id="category_sub">
						<option value="">서브 카테고리 선택</option>
					</select>
				</div>
			</div>
			<div class="d-flex gap-40 align-items-center">
				<label class="form-label">상태</label>
				<div class="d-flex gap-20">
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="all" name="status[]" id="status_all" <?= in_array('all', $statusFilter) ? 'checked' : '' ?>>
					  <label class="form-check-label" for="status_all">전체</label>
					</div>
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="unanswered" name="status[]" id="status_unanswered" <?= in_array('unanswered', $statusFilter) ? 'checked' : '' ?>>
					  <label class="form-check-label" for="status_unanswered">미답변</label>
					</div>
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="answered" name="status[]" id="status_answered" <?= in_array('answered', $statusFilter) ? 'checked' : '' ?>>
					  <label class="form-check-label" for="status_answered">답변완료</label>
					</div>
				</div>
			</div>
			<div class="d-flex justify-content-end column-gap-2">
				<button class="btn btn-outline-secondary btn-sm" type="button" onclick="resetSearch()">초기화</button>
				<button class="btn btn-primary btn-sm" type="submit">검색</button>
			</div>
		</div>
	</div>
</form>

<div class="card">
	<div class="card-header d-flex align-items-center justify-content-between">
		<strong class="title fw-bolder letter-spacing-1">전체 (<?= $totalInquiries ?> 건)</strong>
		<div class="d-flex align-items-center">
			<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="me-2">
				<path d="M16.7857 15.7115H16.25V8.12221C16.25 4.97266 13.9219 2.36998 10.8929 1.93694V1.06864C10.8929 0.575335 10.4933 0.175781 10 0.175781C9.5067 0.175781 9.10714 0.575335 9.10714 1.06864V1.93694C6.07813 2.36998 3.75 4.97266 3.75 8.12221V15.7115H3.21429C2.8192 15.7115 2.5 16.0307 2.5 16.4258V17.1401C2.5 17.2383 2.58036 17.3186 2.67857 17.3186H7.5C7.5 18.6981 8.62054 19.8186 10 19.8186C11.3795 19.8186 12.5 18.6981 12.5 17.3186H17.3214C17.4196 17.3186 17.5 17.2383 17.5 17.1401V16.4258C17.5 16.0307 17.1808 15.7115 16.7857 15.7115ZM10 18.3901C9.40848 18.3901 8.92857 17.9102 8.92857 17.3186H11.0714C11.0714 17.9102 10.5915 18.3901 10 18.3901ZM5.35714 15.7115V8.12221C5.35714 6.88114 5.83929 5.71596 6.71652 4.83873C7.59375 3.9615 8.75893 3.47935 10 3.47935C11.2411 3.47935 12.4063 3.9615 13.2835 4.83873C14.1607 5.71596 14.6429 6.88114 14.6429 8.12221V15.7115H5.35714Z" fill="#1C1C1C"/>
			</svg>
		</div>
	</div>
    <div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>문의번호</th>
                <th>법인명</th>
                <th>담당자명</th>
                <th>연락처</th>
                <th>이메일</th>
                <th>카테고리</th>
                <th>등록일</th>
                <th>상태</th>
                <th>답변일</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($inquiries)): ?>
                <?php foreach ($inquiries as $inquiry): ?>
                <tr onclick="window.location.href='/admin/inquiry/view?id=<?= $inquiry['id'] ?>'" style="cursor: pointer;">
                    <td><?= $inquiry['inquiry_number'] ?></td>
                    <td><?= $inquiry['corporate_name'] ?></td>
                    <td><?= $inquiry['contact_person'] ?></td>
                    <td><?= $inquiry['contact_phone'] ?></td>
                    <td><?= $inquiry['email'] ?></td>
                    <td><?= $inquiry['category_display'] ?></td>
                    <td><?= $inquiry['registration_date'] ?></td>
                    <td><?= $inquiry['status_display'] ?></td>
                    <td><?= $inquiry['answer_date'] ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" class="text-center py-4">
                        <p class="text-muted mb-0">등록된 문의가 없습니다.</p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
    <div class="card-footer">
        <nav aria-label="Inquiry pagination">
            <ul class="pagination justify-content-center mt-4 mb-0">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['current_page' => $page - 1])) ?>">
                            <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M6.49961 11.6501L0.849609 6.0001L6.49961 0.350098L7.54961 1.4001L2.94961 6.0001L7.54961 10.6001L6.49961 11.6501Z" fill="#1C1C1C"/>
                            </svg>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php
                // Improved pagination logic
                if ($totalPages <= 5) {
                    // Show all pages if 5 or fewer pages
                    for ($i = 1; $i <= $totalPages; $i++):
                    ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['current_page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                <?php } else {
                    // For more than 5 pages, use smart pagination
                    $startPage = max(1, $page - 2);
                    $endPage = min($totalPages, $page + 2);
                    
                    // Always show first page
                    if ($startPage > 1) {
                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['current_page' => 1])) . '">1</a></li>';
                        
                        // Show ellipsis if there's a gap
                        if ($startPage > 2) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                    }
                    
                    // Show current range
                    for ($i = $startPage; $i <= $endPage; $i++):
                    ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['current_page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php
                    // Always show last page
                    if ($endPage < $totalPages) {
                        // Show ellipsis if there's a gap
                        if ($endPage < $totalPages - 1) {
                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                        }
                        
                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['current_page' => $totalPages])) . '">' . $totalPages . '</a></li>';
                    }
                }
                ?>
                
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['current_page' => $page + 1])) ?>">
                            <svg width="8" height="12" viewBox="0 0 8 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M1.89961 11.6501L0.849609 10.6001L5.44961 6.0001L0.849609 1.4001L1.89961 0.350098L7.54961 6.0001L1.89961 11.6501Z" fill="#1C1C1C"/>
                            </svg>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
    <?php endif; ?>
</div>

<script>
// Reset search form
function resetSearch() {
    document.querySelector('select[name="search_type"]').value = 'corporate_name';
    document.querySelector('input[name="search_keyword"]').value = '';
    document.querySelector('select[name="category_main"]').value = '';
    document.querySelector('select[name="category_sub"]').value = '';
    document.querySelectorAll('input[name="status[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    // Redirect to first page without search parameters
    window.location.href = window.location.pathname;
}
</script>

<style>
/* Custom pagination styling */
.pagination {
    gap: 8px;
}

.pagination .page-item .page-link {
    border: none;
    background: transparent;
    color: #6c757d;
    padding: 8px 12px;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    transition: all 0.2s ease;
    cursor: pointer;
    pointer-events: auto;
}

.pagination .page-item .page-link:hover {
    background: #f8f9fa;
    color: #495057;
}

.pagination .page-item.active .page-link {
    background: #89b97c;
    color: white;
    border-radius: 50%;
}

.pagination .page-item .page-link svg {
    width: 8px;
    height: 12px;
}

.pagination .page-item .page-link:focus {
    box-shadow: none;
    outline: none;
}

/* Ensure pagination is clickable */
.pagination .page-item {
    pointer-events: auto;
}

.pagination .page-item .page-link {
    pointer-events: auto;
    z-index: 1;
    position: relative;
}

/* Table styling */
.table th {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
    border-bottom: 1px solid #f1f3f4;
}

.table tbody tr:hover {
    background-color: #f8f9fa;
}

/* Form styling */
.form-select, .form-control {
    border: 1px solid #dee2e6;
    border-radius: 6px;
}

.form-select:focus, .form-control:focus {
    border-color: #89b97c;
    box-shadow: 0 0 0 0.2rem rgba(137, 185, 124, 0.25);
}

/* Checkbox styling */
.form-check-input:checked {
    background-color: #89b97c;
    border-color: #89b97c;
}

.form-check-input:focus {
    border-color: #89b97c;
    box-shadow: 0 0 0 0.2rem rgba(137, 185, 124, 0.25);
}
</style>