<?php 
get_header(); ?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
<div class="page-title-box px-3 d-flex align-items-center justify-content-center">
	<h1 class="mb-0 title">고객 문의</h1>
</div>
<section class="home-section">
	<div class="container">
		<d class="d-flex inquiry-wp">
			<form class="form w-100">
				<div class="mb-32">
					<label class="form-label">문의 유형</label>
					<div class="d-flex align-items-center" style="column-gap:13px;">
						<input type="text" class="form-control w-100" name="">
						<svg class="flex-shrink-0" width="24" height="25" viewBox="0 0 24 25" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M15.0502 12.4999L9.3752 18.1499L8.3252 17.0999L12.9252 12.4999L8.3252 7.8999L9.3752 6.8499L15.0502 12.4999Z" fill="#878787"/>
						</svg>
						<input type="text" class="form-control w-100" name="">
					</div>					
				</div>
				<div class="mb-32">
					<label class="form-label">법인명 **없을 경우 이름 작성**</label>
					<input type="text" class="form-control" name="" required>
				</div>
				<div class="mb-32">
					<div class="d-flex column-gap-3">
						<div class="w-100">
							<label class="form-label">담당자 성함</label>
							<input type="text" class="form-control" name="" required>
						</div>
						<div class="w-100">
							<label class="form-label">담당자 연락처</label>
							<input type="text" class="form-control" name="" required>
						</div>
					</div>
				</div>
				<div class="mb-32">
					<label class="form-label">담당자 이메일</label>
					<input type="text" class="form-control" name="" required>
				</div>
				<div class="mb-32">
					<label class="form-label">문의 내용</label>
					<textarea class="form-control" style="min-height: 193px;"></textarea>
				</div>
				<div class="mb-64">
					<div class="form-check">
					  <input class="form-check-input" type="checkbox" value="" id="check-require" required>
					  <label class="form-check-label" for="check-require">
					    개인정보 수집·이용에 동의합니다.
					  </label>
					</div>
				</div>
				<div class="text-center"><button class="btn btn-primary btn-lg" type="submit" disabled>send now</button></div>
			</form>
			<div class="map-wp flex-shrink-0">
				<div id='map' class="mb-32"></div>
				<div class="contact-infor">
					<p><span>상호</span> : 올댓아너스클럽</p>
					<p class="mb-32"><span>주소</span> : 서울특별시 강서구 마곡중앙6로 11, 보타닉파크 3차, 719호</p>
					<p class="line-bottom mb-0">평일 오전 10시~오후 5시</p>
					<a href="tel:+8216000595" class="phone-number">+82 1600-0595</a>
				</div>
			</div>
		</d>
	</div>
</section>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
	var map = L.map('map',{zoomControl: false}).setView([37.538980, 127.071137], 16);

	L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
	}).addTo(map);

	var marker = L.marker([37.538980, 127.071137]);
    marker.bindPopup('서울특별시 강서구 마곡중앙6로 11').openPopup();
    marker.addTo(map);

	jQuery(document).ready(function ($) {
	  
	     var $form = $("form");
	     function checkRequired() {
	    	let $submit = $form.find('button[type="submit"]');
	        let isValid = true;

	        $form.find("input[required]").each(function () {
	            let $field = $(this);
	            if ($field.is(":checkbox")) {          
	                if (!$field.is(":checked")) {
	                    isValid = false;
	                    return false; 
	                }
	            } else {
	                if (!$field.val().trim()) {
	                    isValid = false;
	                    return false;
	                }
	            }
	        });

	        $submit.prop("disabled", !isValid);
	    }

	    $form.find("input[required]").on("input", checkRequired);
	});
</script>
<?php
get_footer(); 
?>