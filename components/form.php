<form class="card">
	<div class="card-header d-flex align-items-center justify-content-between">
		<strong class="title fw-bolder letter-spacing-1">내용 정보</strong>
		<button class="btn btn-primary lh-1" type="submit">수정</button>
	</div>
	<div class="card-body p-0">
		<div class="section-box">
			<strong class="sub-title">좌측</strong>
			<div class="d-flex gap-40 align-items-center">
				<label class="form-label">노출상태</label>
				<input type="text" class="form-control" name="">
			</div>
			<div class="d-flex gap-40 align-items-center">
				<label class="form-label">노출상태</label>
				<div class="d-flex column-gap-3 w-100">
					<input type="text" class="form-control" name="">
					<input type="text" class="form-control" name="">
				</div>
			</div>
		</div>
		<div class="section-box">
			<strong class="sub-title">우측</strong>
			<div class="d-flex gap-40 align-items-center">
				<label class="form-label">설명문구</label>
				<textarea class="form-control"></textarea>
			</div>
		</div>
	</div>
</form>

<form class="card">
	<div class="card-header d-flex align-items-center justify-content-between">
		<strong class="title fw-bolder letter-spacing-1">섹션 정보</strong>
		<button class="btn btn-primary lh-1" type="submit">수정</button>
	</div>
	<div class="card-body p-0">
		<div class="section-box">
			<div class="d-flex gap-40 align-items-center">
				<div class="d-flex gap-40 align-items-center w-100">
					<label class="form-label">노출상태</label>
					<div class="d-flex gap-40 w-100">
						<div class="form-check">
						  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
						  <label class="form-check-label" for="exampleRadios1">
						    노출
						  </label>
						</div>
						<div class="form-check">
						  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option1" checked>
						  <label class="form-check-label" for="exampleRadios2">
						   미노출
						  </label>
						</div>
						<div class="form-check">
						  <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios3" value="option1" disabled>
						  <label class="form-check-label" for="exampleRadios3">
						   미노출
						  </label>
						</div>
					</div>
				</div>
				<div class="d-flex gap-40 align-items-center w-100">
					<label class="form-label">노출상태</label>
					<div class="d-flex gap-20 w-100">
						<input type="text" class="form-control" name="">
						<input type="text" class="form-control" name="">
					</div>
				</div>
			</div>
		</div>
	</div>
</form>