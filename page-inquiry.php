<?php 
get_header(); ?>
<div class="title-box px-3 d-flex align-items-center justify-content-center">
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
			</form>
			<div class="map-wp flex-shrink-0">
				
			</div>
		</d>
	</div>
</section>
<?php
get_footer(); 
?>