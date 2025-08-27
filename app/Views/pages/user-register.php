<div class="container">
	<h1 class="account-title text-center mb-0">SIGN IN</h1>
	<p class="account-subtitle">Customer Information</p>
	<form class="account-form mx-auto form-2-column mb-120 input-white">
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
				<input type="text" class="form-control" required placeholder="">
			</div>
		</div>
		<div class="mb-32">
			<label class="form-label">사업장 주소</label>
			<input type="text" class="form-control" required placeholder="">
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
					<button class="btn btn-primary" type="button">인증</button>
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
</div>