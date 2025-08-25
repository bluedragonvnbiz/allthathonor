<h1 class="page-title">멤버십관리</h1>

<div class="card">
	<div class="card-header d-flex align-items-center justify-content-between">
		<strong class="title fw-bolder letter-spacing-1">전체 (<?= $totalMemberships ?> 건)</strong>
	</div>
    <div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>멤버십 번호</th>
                <th>멤버십명</th>
                <th>상단 문구</th>
                <th>판매가격(원)</th>
                <th>상태</th>
                <th>수정일</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!empty($memberships)): ?>
                <?php
                foreach ($memberships as $membership):
                    $viewUrl = '/admin/membership/view/?id=' . $membership['id'];
                ?>
                <tr>
                    <td><a href="<?= $viewUrl ?>"><?= htmlspecialchars($membership['membership_number']) ?></a></td>
                    <td><a href="<?= $viewUrl ?>"><?= htmlspecialchars($membership['membership_name']) ?></a></td>
                    <td><a href="<?= $viewUrl ?>"><?= htmlspecialchars($membership['top_phrase']) ?></a></td>
                    <td><a href="<?= $viewUrl ?>"><?= number_format($membership['sale_price']) ?></a></td>
                    <td><a href="<?= $viewUrl ?>"><?= $membership['status'] === 'expose' ? '노출' : '미노출' ?></a></td>
                    <td><a href="<?= $viewUrl ?>"><?= date('Y.m.d', strtotime($membership['updated_at'])) ?></a></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center py-4">
                        <p class="text-muted mb-0">등록된 멤버십이 없습니다.</p>
                    </td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>