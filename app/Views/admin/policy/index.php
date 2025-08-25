<h1 class="page-title">약관 관리</h1>

<div class="card">
	<div class="card-header d-flex align-items-center justify-content-between">
		<strong class="title fw-bolder letter-spacing-1">전체 (<?= $totalPolicies ?> 건)</strong>
	</div>
    <div class="card-body">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
            <tr>
                <th>약관명</th>
                <th>파일</th>
                <th>업데이트일</th>
            </tr>
            </thead>
            <tbody>
                <?php if (!empty($policyFiles)): ?>
                    <?php foreach ($policyFiles as $file): ?>
                        <tr>
                            <td>
                                <?= htmlspecialchars($file['policy_name']) ?>
                            </td>
                            <td>
                                <div>
                                    <?php if($file['file_url']): ?>
                                        <a href="<?= $file['file_url'] ?>" class="text-decoration-underline" target="_blank"><?= htmlspecialchars($file['file_name']) ?></a>
                                    <?php else: ?>
                                        <span class="text-muted">파일이 없습니다.</span>
                                    <?php endif; ?>
                                    <input type="hidden" name="policy_<?= $file['id'] ?: $file['policy_type_key'] ?>" value="<?= htmlspecialchars($file['file_url']) ?>">
                                    <button type="button" class="btn btn-primary" onclick="openPolicyMediaLibrary('<?= $file['policy_type_key'] ?? '' ?>', '<?= htmlspecialchars($file['policy_name']) ?>', <?= $file['id'] ?: 'null' ?>)">
                                        수정
                                    </button>
                                </div>
                            </td>
                            <td>
                                <?= $file['updated_date'] ? date('Y.m.d', strtotime($file['updated_date'])) : '-' ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
    .upload-box input {
        width: auto;
    }
    table.table .btn {
        display: inline-block;
        width: auto;
        margin: 0 0 0 10px;
    }
</style>

<script>
// Open WordPress Media Library for policy upload
function openMediaLibrary() {
    openPolicyMediaLibrary('', '', null);
}

// Open Media Library for specific policy type
function openPolicyMediaLibrary(policyTypeKey, policyName, attachmentId) {
    if (typeof wp !== 'undefined' && wp.media) {
        const frame = wp.media({
            title: policyTypeKey ? '파일 업로드 - ' + policyName : '약관 파일 업로드',
            button: { text: '업로드' },
            multiple: false,
            library: { 
                type: ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']
            }
        });

        // Pre-select existing file if exists
        if (attachmentId && attachmentId !== 'null') {
            frame.on('open', function() {
                const selection = frame.state().get('selection');
                const attachment = wp.media.attachment(attachmentId);
                attachment.fetch();
                selection.add(attachment);
            });
        }

        frame.on('select', function() {
            const attachment = frame.state().get('selection').first().toJSON();
            
            if (policyTypeKey) {
                // Update attachment with default policy type
                updatePolicyMetadata(attachment.id, policyName, policyTypeKey);
            } else {
                // Set policy type metadata
                const policyType = prompt('약관명을 입력하세요:', attachment.title);
                if (policyType) {
                    // Update attachment with policy metadata
                    updatePolicyMetadata(attachment.id, policyType);
                }
            }
        });

        frame.open();
    } else {
        alert('미디어 라이브러리를 사용할 수 없습니다.');
    }
}

// Update policy metadata via AJAX
function updatePolicyMetadata(attachmentId, policyType, policyTypeKey = null) {
    fetch(define.ajax_url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({
            action: 'update_policy_metadata',
            attachment_id: attachmentId,
            policy_type: policyType,
            policy_type_key: policyTypeKey,
            nonce: '<?= wp_create_nonce('update_policy_metadata') ?>'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Refresh to show new file
        } else {
            alert('메타데이터 업데이트 실패: ' + data.data.message);
        }
    });
}

// Edit policy
function editPolicy(attachmentId) {
    const newName = prompt('새로운 약관명을 입력하세요:');
    if (newName) {
        updatePolicyMetadata(attachmentId, newName);
    }
}

// Delete policy
function deletePolicy(attachmentId) {
    if (confirm('이 약관 파일을 삭제하시겠습니까?')) {
        fetch(define.ajax_url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'delete_policy_file',
                attachment_id: attachmentId,
                nonce: '<?= wp_create_nonce('delete_policy_file') ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('삭제 실패: ' + data.data.message);
            }
        });
    }
}

</script>