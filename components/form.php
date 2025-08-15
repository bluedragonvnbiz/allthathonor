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

<form class="card">
	<div class="card-header d-flex align-items-center justify-content-between">
		<strong class="title">혜택 정보</strong>
		<button class="btn btn-primary lh-1" type="submit">저장</button>
	</div>
	<div class="card-body p-0">
		<div class="section-box">
			<nav class="nav-tab-btn d-flex align-items-center">
				<button class="btn p-0 border-0 active" type="button">트래블케어</button>
				<button class="btn p-0 border-0" type="button">라이프스타일</button>
				<button class="btn p-0 border-0" type="button">스페셜베네핏</button>
				<button class="btn p-0 border-0" type="button">웰컴기프트</button>
			</nav>
		</div>
		<div class="section-box">			
			<table class="table table-hover mb-0 table-input">
				<thead>
					<tr>
						<th style="width: 180px;">제공혜택</th>
						<th>설명</th>
						<th style="width: 80px;">사용여부</th> 
						<th style="width: 80px;">
							<div class="d-flex align-items-center justify-content-center">
								<span>요약혜택</span>
								<div class="pv-tooltip">
									<svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
										<circle cx="10.0003" cy="10.0003" r="7.5" stroke="#A0A5B5" stroke-width="1.66667"/>
										<circle cx="9.99967" cy="13.1252" r="1.04167" fill="#A0A5B5"/>
										<path d="M7.91699 7.91691C7.91699 6.99644 8.84973 6.25024 10.0003 6.25024C11.1509 6.25024 12.0837 6.99644 12.0837 7.91691C12.0837 8.72371 11.3671 9.39662 10.4153 9.55051C10.1881 9.58725 10.0003 9.77013 10.0003 10.0002V10.4169" stroke="#A0A5B5" stroke-width="1.66667" stroke-linecap="round"/>
									</svg>
									<div class="tooltip-content">
										멤버십마다 사용 중인 혜택중에서 최대<br>3개의 요약혜택만 선택할 수 있으며, <br>이 3개의 요약혜택은 멤버십<br> 페이지의 섹션 1에 표시됩니다.
									</div>
								</div>
							</div>
						</th>				      
					</tr>
				</thead>
				<tbody>
				<?php  
				$arr = [
					["공항 의전 서비스"],
					["VIP 쇼퍼 서비스"],
					["공항 의전 서비스"],
					["24시간 해외 트래블 케어"],
					["전용기 예약 지원"],
					["인천공항 라운지 이용권"],
				];
				foreach ($arr as $key => $value) { ?>
					<tr>
						<td><?= $value[0] ?></td>
						<td style="text-align:left;">내용에 대한 설명입니다.</td>
						<td>
							<div class="form-check checkbox justify-content-center">
								<input class="form-check-input <?= rand(0, 1) ? 'black' : '' ?>" type="checkbox" value="" name="" <?= rand(0, 1) ? 'disabled' : '' ?> <?= rand(0, 1) ? 'checked' : '' ?>>
							</div>
						</td>
						<td>
							<div class="form-check checkbox justify-content-center">
								<input class="form-check-input <?= rand(0, 1) ? 'black' : '' ?>" type="checkbox" value="" name="" <?= rand(0, 1) ? 'disabled' : '' ?> <?= rand(0, 1) ? 'checked' : '' ?>>
							</div>
						</td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>			
		</div>
		<div class="section-box border-0">
			<div class="d-flex column-gap-30">
				<label class="form-label">이용안내</label>
				<textarea class="form-control" placeholder="" style="height: 126px;">이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.
이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.
이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.이용안내에 대한 설명입니다.</textarea>
			</div>
		</div>
	</div>
</form>