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
			<div class="d-flex gap-40 align-items-start">
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
				<select class="form-select w-100">
					<option>1</option>
					<option>2</option>
					<option>3</option>
				</select>
			</div>			
		</div>
	</div>
</form>

<form class="card">
	<div class="card-body p-0">
		<div class="section-box p-4 border-0">
			<div class="d-flex gap-40 align-items-center w-100">
				<label class="form-label">검색</label>
				<div class="d-flex column-gap-2 w-100">
					<select class="form-select">
						<option>1</option>
						<option>2</option>
						<option>3</option>
					</select>
					<input type="text" class="form-control" name="" placeholder="검색어를 입력하세요.">
				</div>
			</div>
			<div class="d-flex gap-40 align-items-center w-100">
				<label class="form-label">카테고리</label>
				<div class="d-flex column-gap-2 w-100">
					<select class="form-select">
						<option>카테고리 1차</option>
						<option>2</option>
						<option>3</option>
					</select>
					<select class="form-select">
						<option>카테고리 2차</option>
						<option>2</option>
						<option>3</option>
					</select>
				</div>
			</div>
			<div class="d-flex gap-40 align-items-center">
				<label class="form-label">상태</label>
				<div class="d-flex gap-20">
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
					  <label class="form-check-label" for="defaultCheck1">전체</label>
					</div>
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="" id="defaultCheck2">
					  <label class="form-check-label" for="defaultCheck2">미답변</label>
					</div>
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="" id="defaultCheck3">
					  <label class="form-check-label" for="defaultCheck3">답변완료</label>
					</div>
				</div>
			</div>
			<div class="d-flex justify-content-end column-gap-2">
				<button class="btn btn-outline-secondary btn-sm" type="button">초기화</button>
				<button class="btn btn-primary btn-sm" type="submit">검색</button>
			</div>
		</div>
	</div>
</form>
<form class="card">
	<div class="card-body p-0">
		<div class="section-box p-4 border-0">
			<div class="d-flex gap-40">
				<label class="form-label">검색</label>
				<div class="d-flex column-gap-2 w-100">
					<select class="form-select">
						<option>카테고리 1차</option>
						<option>2</option>
						<option>3</option>
					</select>
					<input type="text" class="form-control" placeholder="검색어를 입력하세요." name="">
				</div>
			</div>
			<div class="d-flex gap-40">
				<label class="form-label">등급</label>
				<div class="d-flex gap-20">
				<?php  
				$arr = ["전체","signature","pRIMe","미분류"];
				foreach ($arr as $key => $value) { ?>
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="" id="abc<?= $key ?>">
					  <label class="form-check-label text-uppercase" for="abc<?= $key ?>"><?= $value ?></label>
					</div>
				<?php
				}
				?>
				</div>
			</div>
			<div class="d-flex gap-40">
				<label class="form-label">유형</label>
				<div class="d-flex gap-20">
				<?php  
				$arr = ["전체","Wellness","Event invitation","미분류"];
				foreach ($arr as $key => $value) { ?>
					<div class="form-check checkbox">
					  <input class="form-check-input" type="checkbox" value="" id="def<?= $key ?>">
					  <label class="form-check-label text-uppercase" for="def<?= $key ?>"><?= $value ?></label>
					</div>
				<?php
				}
				?>
				</div>
			</div>
			<div class="d-flex justify-content-end column-gap-2">
				<button class="btn btn-outline-secondary btn-sm" type="button">초기화</button>
				<button class="btn btn-primary btn-sm" type="submit">검색</button>
			</div>
		</div>
	</div>
</form>

<form class="card">
	<div class="card-header d-flex align-items-center justify-content-between">
		<strong class="title">상품 내용</strong>
		<button class="btn btn-primary lh-1" type="submit">수정</button>
	</div>
	<div class="card-body p-0">
		<div class="section-box">
			<div class="d-flex gap-40">
				<div class="d-flex column-gap-30 w-100 align-items-center">
				 	<label class="form-label">노출상태</label>
				 	<div class="d-flex column-gap-20">
				 		<div class="form-check">
						  <input class="form-check-input" type="radio" name="example1" id="aas1" value="option1" checked="">
						  <label class="form-check-label" for="aas1">
						    노출
						  </label>
						</div>
						<div class="form-check">
						  <input class="form-check-input" type="radio" name="example1" id="aas2" value="option1" checked="">
						  <label class="form-check-label" for="aas2">
						   미노출
						  </label>
						</div>
				 	</div>
				</div>
				<div class="d-flex column-gap-30 w-100 align-items-center">
					<label class="form-label">메인 이미지</label>
					<div class="upload-box w-100">
						<input type="text" class="form-control file-name" readonly placeholder="파일을 선택해주세요.">
						<label class="btn btn-primary">
							파일 선택
							<input type="file" class="d-none select-file" name="" accept=".pdf, .jpg, .jpeg, .png, .heic">
						</label>
					</div>
				</div>
			</div>		
		</div>
		<div class="section-box">
			<div class="d-flex gap-40">
				<div class="d-flex column-gap-30 align-items-center w-100">
					<label class="form-label">상품명</label>
					<input type="text" class="form-control" name="" placeholder="내용을 입력해주세요.">
				</div>
				<div class="d-flex column-gap-30 align-items-center w-100">
					<label class="form-label">상품명(영문)</label>
					<input type="text" class="form-control" name="" placeholder="내용을 입력해주세요.">
				</div>
			</div>			
		</div>
		<div class="section-box">
			<div class="d-flex column-gap-30">
				<label class="form-label">요약 설명</label>
				<textarea class="form-control" placeholder="내용을 입력해주세요."></textarea>
			</div>
		</div>
		<div class="section-box d-block">
			<label class="form-label mb-3">상세 설명</label>
			<textarea class="form-control" style="min-height: 300px;" placeholder="내용을 입력해주세요."></textarea>
		</div>
	</div>
</form>