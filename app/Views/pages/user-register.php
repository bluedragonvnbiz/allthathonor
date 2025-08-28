<div class="container">
	<!--step 1--->
	<h1 class="account-title text-center mb-0">SIGN IN</h1>
	<p class="account-subtitle">Customer Information</p>
	<form class="account-form mx-auto form-2-column mb-120 input-white register-form">
		<div class="mb-32">
			<label class="form-label">ID</label>
			<input type="text" class="form-control" required placeholder="영어 소문자, 숫자를 조합하여 6~12자리">
		</div>
		<div class="d-flex column-gap-3 mb-32">
			<div class="w-100">
				<label class="form-label">비밀번호</label>
				<input type="text" class="form-control" required placeholder="영어 대소문자, 숫자를 조합하여 8~16자리">
			</div>
			<div class="w-100">
				<label class="form-label">비밀번호 확인</label>
				<input type="text" class="form-control" required placeholder="">
			</div>
		</div>
		<div class="d-flex column-gap-3 mb-32">
			<div class="w-100">
				<label class="form-label">법인명</label>
				<input type="text" class="form-control" required placeholder="">
			</div>
			<div class="w-100">
				<label class="form-label">법인명 (영문)</label>
				<input type="text" class="form-control" required placeholder="">
			</div>
		</div>
		<div class="d-flex column-gap-3 mb-32">
			<div class="w-100">
				<label class="form-label">사업자등록증</label>
				<div class="group-element">
					<input type="text" class="form-control file-name" readonly name="">
					<label class="btn btn-primary d-flex align-items-center justify-content-center">파일
						<input type="file" class="d-none input-file" name="">
					</label>
				</div>
			</div>
			<div class="w-100">
				<label class="form-label">사업자등록번호</label>
				<input type="text" class="form-control biznum" required placeholder="">
			</div>
		</div>
		<div class="mb-32">
			<label class="form-label">사업장 주소</label>
			<input type="text" class="form-control is-invalid" required placeholder="">
			<div class="invalid-feedback"> Please provide a valid city.</div>
		</div>
		<div class="d-flex column-gap-3 mb-32">
			<div class="w-100">
				<label class="form-label">대표자명</label>
				<input type="text" class="form-control" required placeholder="">
			</div>
			<div class="w-100">
				<label class="form-label">대표자 연락처</label>
				<input type="text" class="form-control" required placeholder="">
			</div>
		</div>
		<div class="d-flex column-gap-3 mb-32">
			<div class="w-100">
				<label class="form-label">담당자명</label>
				<input type="text" class="form-control" required placeholder="">
			</div>
			<div class="w-100">
				<label class="form-label">담당자 연락처</label>
				<div class="group-element">
					<input type="tel" inputmode="numeric" class="form-control phone-number" name="">
					<button class="btn btn-primary" type="button" data-bs-toggle="modal" data-bs-target="#alert-modal">인증</button>
				</div>
			</div>
		</div>
		<div class="d-flex column-gap-3 mb-32">
			<div class="w-100">
				<label class="form-label">담당자 이메일</label>
				<input type="text" class="form-control" required placeholder="">
			</div>
			<div class="w-100">
				<label class="form-label">세금계산서 수신 이메일</label>
				<input type="text" class="form-control" required placeholder="">
			</div>
		</div>
		<div class="text-center"><button class="btn btn-primary btn-lg" type="submit" disabled>다음</button></div>
	</form>
	<!--end step 1--->
	<h1 class="account-title text-center mb-0">SIGN IN</h1>
	<p class="account-subtitle">Membership</p>
	<form class="account-form mb-120 input-white register-form">
		<?php  
		foreach ([["Signature","signature-card.png","10,000,000원"],["Prime","prime-card.png","5,000,000원"]] as $key => $value) { ?>
			<div class="membership-item">
				<div class="title-box d-flex align-items-center justify-content-between">
					<strong class="title"><?= $value[0] ?></strong>
					<a href="#" class="btn btn-outline-primary fw-medium">view detail</a>
				</div>
				<div class="content d-flex gap-35">
					<div class="img flex-shrink-0">
						<img src="<?= THEME_URL."/assets/images/".$value[1] ?>">
					</div>
					<div class="w-100">
						<div class="d-flex gap-35">
							<div class="w-100">
								<ul class="list-unstyled mb-32 d-flex flex-column row-gap-2">
									<li><strong>BENEFIT 1</strong><span>tVivamus volutpat eros pulvinar v</span></li>
									<li><strong>BENEFIT 2</strong><span>tVivamus volutpat eros pulvinar v</span></li>
									<li><strong>BENEFIT 3</strong><span>tVivamus volutpat eros pulvinar v</span></li>
								</ul>
								<div class="price-box d-flex align-items-end column-gap-2 mb-32">
									<strong><?= $value[2] ?></strong>
									<span>세금 별도</span>
								</div>
								<div class="custom-number-input">
								    <input type="number" class="form-control" value="1" min="1">
								    <div class="arrows">
								        <button type="button" class="up-arrow p-0 border-0">
								        	<svg width="9" height="8" viewBox="0 0 9 8" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M5.37956 0.499991C4.99466 -0.166672 4.03241 -0.166669 3.64751 0.499996L0.183408 6.5C-0.201492 7.16667 0.279633 8 1.04943 8H7.97768C8.74748 8 9.2286 7.16666 8.8437 6.5L5.37956 0.499991Z" fill="#878787"/>
													</svg>
								        </button>
								        <button type="button" class="down-arrow p-0 border-0">
								        	<svg width="9" height="8" viewBox="0 0 9 8" fill="none" xmlns="http://www.w3.org/2000/svg">
													<path d="M5.37956 7.50001C4.99466 8.16667 4.03241 8.16667 3.64751 7.5L0.183408 1.5C-0.201492 0.833333 0.279633 0 1.04943 0H7.97768C8.74748 0 9.2286 0.833337 8.8437 1.5L5.37956 7.50001Z" fill="#878787"/>
													</svg>
								        </button>
								    </div>
								</div>
							</div>
							<div class="right flex-shrink-0 d-flex flex-column justify-content-end align-items-end row-gap-3">
								<strong class="text-gold">총 상품 금액</strong>
								<strong>20,000원</strong>
							</div>
						</div>
					</div>					
				</div>
			</div>
		<?php
		}
		?>
		<div class="summary-box d-flex gap-35 mb-64">
			<div class="text-center w-100">
				<p>총 상품금액</p>
				<strong>25,000,000원</strong>
			</div>
			<div class="text-center w-100">
				<p>부가세</p>
				<strong>25,000원</strong>
			</div>
			<div class="text-center w-100">
				<p>총 결제 금액</p>
				<strong>27,000,000원</strong>
			</div>
		</div>
		<div class="text-center"><button class="btn btn-primary btn-lg" type="submit">다음</button></div>
	</form>
	<!--end step 2--->
	<h1 class="account-title text-center mb-0">SIGN IN</h1>
	<p class="account-subtitle">payment</p>
	<form class="account-form mb-120 input-white register-form">
		
		<div class="payment-box mb-64">
			<div class="border-box mb-32">
				<strong class="title">약관동의</strong>
				<div class="form-check mb-3">
				  <input class="form-check-input" type="checkbox" value="" id="check-all">
				  <label class="form-check-label" for="check-all">전체 동의 </label>
				</div>
				<ul class="list-unstyled mb-0 d-flex flex-column row-gap-3">
				<?php  
				$arr = [
					["서비스 이용약관","(필수)"],
					["개인정보 수집 및 이용 동의","(필수)"],
					["부속동의서","(필수)"],
					["마케팅 목적 활용 동의","(선택)"],
					["제 3자 정보 제공 동의","(선택)"]
				];
				foreach ($arr as $key => $value) { ?>
					<li>
						<div class="form-check">
						  <input class="form-check-input" type="checkbox" value="" id="check-<?= $key ?>">
						  <label class="form-check-label" for="check-<?= $key ?>"><?= $value[0]." <span>".$value[1]."</span>" ?> </label>
						</div>
						<button class="btn p-0 border-0 " type="button" data-bs-toggle="modal" data-bs-target="#pdf-modal">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M9 18L15 12L9 6" stroke="#1C1C1C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</button>
					</li>
				<?php
				}
				?>
				</ul>
			</div>
			<div class="border-box">
				<strong class="title">결제방법</strong>
				<div class="d-flex column-gap-3">
					<button class="btn btn-primary btn-lg" type="button">카드 결제</button>
					<button class="btn btn-outline-secondary btn-lg h-auto" type="button">계좌이체</button>
				</div>
			</div>
		</div>
		<div class="text-center"><button class="btn btn-primary btn-lg" type="submit">다음</button></div>
	</form>
	<!--end step 3--->
</div>

<!--modal--->
<div class="modal fade alert-modal" id="alert-modal" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-body p-4">
        <strong class="title">인증번호를 입력해주세요.</strong>
        <input type="text" class="form-control mb-4 bg-white text-center is-invalid" name="" value="083628">
        <div class="d-flex column-gap-3 group-action-btn pt-4">
        	<button type="button" class="btn btn-outline-primary w-100 btn-lg" data-bs-dismiss="modal">취소</button>
        	<button type="button" class="btn btn-primary w-100 btn-lg">확인</button>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="modal fade alert-modal" id="pdf-modal">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body p-4">
        <strong class="title text-start">서비스 이용약관 </strong>
        <iframe src="<?= THEME_URL."/assets/images/demo/file-sample.pdf" ?>" width="100%" height="600px"></iframe>
        <div class="d-flex justify-content-center group-action-btn pt-4">
        	<button type="button" class="btn btn-outline-primary" style="min-width: 270px;" data-bs-dismiss="modal">닫기</button>
        </div>
      </div>
    </div>
  </div>
</div>
<!--end modal--->