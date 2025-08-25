<form class="card" method="GET" action="">
	<div class="card-body p-0">
		<div class="section-box p-4 border-0">
			<div class="d-flex gap-40 align-items-center w-100">
				<label class="form-label">검색</label>
				<div class="d-flex column-gap-2 w-100">
					<select class="form-select" name="search_type">
						<option value="voucher_name" <?= $searchType === 'voucher_name' ? 'selected' : '' ?>>혜택/바우처명</option>
						<option value="voucher_code" <?= $searchType === 'voucher_code' ? 'selected' : '' ?>>혜택/바우처 코드</option>
					</select>
					<input type="text" class="form-control" name="search_keyword" placeholder="검색어를 입력하세요." value="<?= htmlspecialchars($searchKeyword) ?>">
				</div>
			</div>
			<div class="d-flex gap-40 align-items-center">
				<label class="form-label">등급</label>
				<div class="d-flex gap-20">
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="all" name="grade[]" id="grade_all" <?= in_array('all', $gradeFilter) ? 'checked' : '' ?>>
					  <label class="form-check-label" for="grade_all">전체</label>
					</div>
					<?php if (!empty($availableGrades)): ?>
						<?php foreach ($availableGrades as $grade): ?>
							<div class="form-check checkbox">
							  <input class="form-check-input" type="checkbox" value="<?= $grade->grade_id ?>" name="grade[]" id="grade_<?= $grade->grade_id ?>" <?= in_array($grade->grade_id, $gradeFilter) ? 'checked' : '' ?>>
							  <label class="form-check-label" for="grade_<?= $grade->grade_id ?>"><?= htmlspecialchars($grade->grade_name) ?></label>
							</div>
						<?php endforeach; ?>
					<?php else: ?>
						<!-- Fallback if no grades found in database -->
						<div class="form-check checkbox">
						  <input class="form-check-input" type="checkbox" value="signature" name="grade[]" id="grade_signature" <?= in_array('signature', $gradeFilter) ? 'checked' : '' ?>>
						  <label class="form-check-label" for="grade_signature">SIGNATURE</label>
						</div>
						<div class="form-check checkbox">
						  <input class="form-check-input" type="checkbox" value="prime" name="grade[]" id="grade_prime" <?= in_array('prime', $gradeFilter) ? 'checked' : '' ?>>
						  <label class="form-check-label" for="grade_prime">PRIME</label>
						</div>
					<?php endif; ?>
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="unclassified" name="grade[]" id="grade_unclassified" <?= in_array('unclassified', $gradeFilter) ? 'checked' : '' ?>>
					  <label class="form-check-label" for="grade_unclassified">미분류</label>
					</div>
				</div>
			</div>
			<div class="d-flex gap-40 align-items-center">
				<label class="form-label">유형</label>
				<div class="d-flex gap-20">
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="all" name="type[]" id="type_all" <?= in_array('all', $typeFilter) ? 'checked' : '' ?>>
					  <label class="form-check-label" for="type_all">전체</label>
					</div>
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="voucher" name="type[]" id="type_voucher" <?= in_array('voucher', $typeFilter) ? 'checked' : '' ?>>
					  <label class="form-check-label" for="type_voucher">VOUCHER</label>
					</div>
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="event_invitation" name="type[]" id="type_event_invitation" <?= in_array('event_invitation', $typeFilter) ? 'checked' : '' ?>>
					  <label class="form-check-label" for="type_event_invitation">EVENT INVITATION</label>
					</div>
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="unclassified" name="type[]" id="type_unclassified" <?= in_array('unclassified', $typeFilter) ? 'checked' : '' ?>>
					  <label class="form-check-label" for="type_unclassified">미분류</label>
					</div>
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
		<strong class="title fw-bolder letter-spacing-1">전체 (<?= $totalVouchers ?> 건)</strong>
		<a href="/admin/voucher/add" class="btn btn-primary lh-1">+ 새 추가</a>
	</div>
    <div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>혜택/바우처 번호</th>
                <th>등급</th>
                <th>유형</th>
                <th>혜택/바우처명</th>
                <th>상태</th>
                <th>등록일</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($vouchers)): ?>
                <?php
                foreach ($vouchers as $voucher):
                    // Add current page and search params to edit URL
                    $currentParams = $_GET;
                    $currentParams['id'] = $voucher['id'];
                    $viewUrl = '/admin/voucher/edit/?' . http_build_query($currentParams);
                    
                    // Format grade display - convert membership IDs to names
                    $gradeDisplay = '--';
                    if (!empty($voucher['category'])) {
                        $gradeIds = explode(',', $voucher['category']);
                        $gradeNames = [];
                        
                        foreach ($gradeIds as $gradeId) {
                            $gradeId = trim($gradeId);
                            if (is_numeric($gradeId)) {
                                // Find corresponding grade name from availableGrades
                                foreach ($availableGrades as $grade) {
                                    if ($grade->grade_id == $gradeId) {
                                        $gradeNames[] = $grade->grade_name;
                                        break;
                                    }
                                }
                            }
                        }
                        
                        if (!empty($gradeNames)) {
                            if (count($gradeNames) > 1) {
                                $gradeDisplay = implode('/', $gradeNames);
                            } else {
                                $gradeDisplay = $gradeNames[0];
                            }
                        }
                    }
                    
                    // Format type display
                    $typeDisplay = '--';
                    if (!empty($voucher['type'])) {
                        $types = explode(',', $voucher['type']);
                        if (count($types) > 1) {
                            $typeDisplay = 'Voucher/ Event';
                        } else {
                            $typeDisplay = strtoupper($types[0]);
                        }
                    }
                ?>
                <tr onclick="window.location.href='<?= $viewUrl ?>'">
                    <td>BF<?= str_pad($voucher['id'], 6, '0', STR_PAD_LEFT) ?></td>
                    <td><?= htmlspecialchars($gradeDisplay) ?></td>
                    <td><?= htmlspecialchars($typeDisplay) ?></td>
                    <td><?= htmlspecialchars($voucher['name']) ?></td>
                    <td><?= $voucher['status'] === 'expose' ? '노출' : '미노출' ?></td>
                    <td><?= date('Y.m.d', strtotime($voucher['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <p class="text-muted mb-0">등록된 혜택/바우처가 없습니다.</p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <?php if ($totalPages > 1): ?>
    <div class="card-footer">
        <nav aria-label="Voucher pagination">
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
    document.querySelector('select[name="search_type"]').value = 'voucher_name';
    document.querySelector('input[name="search_keyword"]').value = '';
    document.querySelectorAll('input[name="grade[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.querySelectorAll('input[name="type[]"]').forEach(checkbox => {
        checkbox.checked = false;
    });
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
