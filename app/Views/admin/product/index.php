<form class="card" method="GET" action="">
	<div class="card-body p-0">
		<div class="section-box p-4 border-0">
			<div class="d-flex gap-40 align-items-center w-100">
				<label class="form-label">검색</label>
				<div class="d-flex column-gap-2 w-100">
					<select class="form-select" name="search_type">
						<option value="product_name" <?= $searchType === 'product_name' ? 'selected' : '' ?>>상품명</option>
						<option value="product_name_en" <?= $searchType === 'product_name_en' ? 'selected' : '' ?>>상품명 (영문)</option>
					</select>
					<input type="text" class="form-control" name="search_keyword" placeholder="검색어를 입력하세요." value="<?= htmlspecialchars($searchKeyword) ?>">
				</div>
			</div>
			<div class="d-flex gap-40 align-items-center">
				<label class="form-label">상태</label>
				<div class="d-flex gap-20">
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="expose" name="status[]" id="status_expose" <?= in_array('expose', $statusFilter) ? 'checked' : '' ?>>
					  <label class="form-check-label" for="status_expose">노출</label>
					</div>
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="not_expose" name="status[]" id="status_not_expose" <?= in_array('not_expose', $statusFilter) ? 'checked' : '' ?>>
					  <label class="form-check-label" for="status_not_expose">미노출</label>
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
		<strong class="title fw-bolder letter-spacing-1">전체 (<?= $totalProducts ?> 건)</strong>
		<a href="/admin/product/add" class="btn btn-primary lh-1">+ 새 추가</a>
	</div>
    <div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>상품번호</th>
                <th>상품명</th>
                <th>상품명 (영문)</th> 
                <th>상태</th>
                <th>등록일</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($products)): ?>
                <?php
                foreach ($products as $product):
                    $viewUrl = '/admin/product/view/?id=' . $product['id'];
                ?>
                <tr onclick="window.location.href='<?= $viewUrl ?>'">
                    <td>PT<?= str_pad($product['id'], 6, '0', STR_PAD_LEFT) ?></td>
                    <td><?= htmlspecialchars($product['product_name']) ?></td>
                    <td><?= htmlspecialchars($product['product_name_en']) ?></td>
                    <td><?= $product['exposure_status'] === 'expose' ? '노출' : '미노출' ?></td>
                    <td><?= date('Y-m-d', strtotime($product['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center py-4">
                        <p class="text-muted mb-0">등록된 상품이 없습니다.</p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
    <div class="card-footer">
        <nav aria-label="Product pagination">
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
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                    <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                        <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['current_page' => $i])) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                
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
    document.querySelector('select[name="search_type"]').value = 'product_name';
    document.querySelector('input[name="search_keyword"]').value = '';
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
</style>
