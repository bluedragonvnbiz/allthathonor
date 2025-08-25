<h1 class="page-title">1:1 문의 > 문의 상세</h1>
<div class="card infor-box">
    <div class="card-header d-flex align-items-center justify-content-between">
        <strong class="title fw-bolder letter-spacing-1">문의 정보</strong>
    </div>
    <div class="card-body p-0">
        <div class="section-box">
            <div class="d-flex gap-40">
                <div class="d-flex column-gap-30 w-100 align-items-center">
                    <label class="form-label">문의번호</label>
                    <span class="inquiry-display-text"><?php echo esc_html($inquiry['inquiry_number'] ?? ''); ?></span>
                </div>
                <div class="d-flex column-gap-30 w-100 align-items-center">
                    <label class="form-label">등록일</label>
                    <span class="inquiry-display-text"><?php echo esc_html($inquiry['registration_date'] ?? ''); ?></span>
                </div>
            </div>
            <div class="d-flex gap-40">
                <div class="d-flex column-gap-30 w-100 align-items-center">
                    <label class="form-label">법인명</label>
                    <span class="inquiry-display-text"><?php echo esc_html($inquiry['corporate_name'] ?? ''); ?></span>
                </div>
                <div class="d-flex column-gap-30 w-100 align-items-center">
                    <label class="form-label">담당자</label>
                    <span class="inquiry-display-text"><?php echo esc_html($inquiry['contact_person'] ?? ''); ?></span>
                </div>
            </div>
            <div class="d-flex gap-40">
                <div class="d-flex column-gap-30 w-100 align-items-center">
                    <label class="form-label">이메일</label>
                    <span class="inquiry-display-text"><?php echo esc_html($inquiry['email'] ?? ''); ?></span>
                </div>
                <div class="d-flex column-gap-30 w-100 align-items-center">
                    <label class="form-label">연락처</label>
                    <span class="inquiry-display-text"><?php echo esc_html($inquiry['contact_phone'] ?? ''); ?></span>
                </div>
            </div>
        </div>
        <div class="section-box">
            <div class="d-flex gap-40">
                <div class="d-flex column-gap-30 w-100 align-items-center">
                    <label class="form-label">문의내용</label>
                    <span class="display-text"><?php echo nl2br(esc_html($inquiry['inquiry_content'] ?? '문의내용입니다.')); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
<form class="card" id="answer-form">
    <?php wp_nonce_field('submit_inquiry_answer', 'inquiry_answer_nonce'); ?>
    <input type="hidden" name="action" value="submit_inquiry_answer">
    <input type="hidden" name="inquiry_id" value="<?php echo esc_attr($inquiry['id'] ?? ''); ?>">
    
    <div class="card-header d-flex align-items-center justify-content-between">
        <strong class="title fw-bolder letter-spacing-1">답변 작성</strong>
        <?php if ($inquiry['status'] == 'unanswered') : ?>
            <button class="btn btn-primary btn-submit-answer lh-1" type="submit" disabled>전송</button>
        <?php endif; ?>
    </div>
    <div class="card-body p-0">
        <div class="section-box">
            <div class="d-flex gap-40">
                <div class="d-flex column-gap-30 w-100 align-items-center">
                    <label class="form-label">답변상태</label>
                    <span class="inquiry-display-text"><?php echo esc_html($inquiry['status'] == 'unanswered' ? '미답변' : '답변완료'); ?></span>
                </div>
                <div class="d-flex column-gap-30 w-100 align-items-center">
                    <label class="form-label">답변일</label>
                    <span class="inquiry-display-text"><?php echo esc_html($inquiry['answer_date'] ?? '-'); ?></span>
                </div>
            </div>
        </div>
        <div class="section-box">
            <?php if ($inquiry['status'] == 'unanswered') : ?>
                <textarea class="form-control rich-text-editor" name="answer_content" id="answer_content" rows="10"></textarea>
            <?php else : ?>
                <div class="d-flex column-gap-30 w-100 align-items-center">
                    <span class="display-text"><?php echo htmlspecialchars_decode($inquiry['answer_content'] ?? '답변내용입니다.'); ?></span>
                </div>
            <?php endif; ?>
        </div>
    </div>
</form>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmSubmitModal" tabindex="-1" aria-labelledby="confirmSubmitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmSubmitModalLabel">답변 전송 확인</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>답변을 전송하시겠습니까?</p>
                <p class="text-muted small">전송 후에는 수정할 수 없습니다.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="padding:14px 11px;">취소</button>
                <button type="button" class="btn btn-primary" id="confirmSubmitBtn">전송</button>
            </div>
        </div>
    </div>
</div>