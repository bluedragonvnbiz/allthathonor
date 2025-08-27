<div class="container">
	<h1 class="account-title text-center mb-35">LOG IN</h1>
	<form class="account-form mx-auto mb-120 form-1-column">
		<div class="mb-32">
			<label class="form-label">ID</label>
			<input type="text" class="form-control" required>
		</div>
		<div class="mb-32">
			<label class="form-label">PW</label>
			<input type="password" class="form-control" required>
		</div>
		<div class="d-flex gap-35 flex-column">
			<div class="d-flex align-items-center justify-content-between">
				<a href="/register/">회원가입</a>
				<div class="d-flex align-items-center" style="column-gap:9px;">
					<a href="#">ID 찾기</a>
					<span class="dot"></span>
					<a href="#">PW 찾기</a>
				</div>
			</div>
			<button class="btn btn-primary w-100" type="submit">로그인</button>
			<a href="#" class="btn btn-outline-secondary">비회원 주문 확인</a>
		</div>
	</form>
</div>