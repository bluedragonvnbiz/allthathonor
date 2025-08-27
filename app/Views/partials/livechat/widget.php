<!-- LiveChat Widget -->
<div class="chat-box dropup">
	<button type="button" class="btn p-0 border-0 open-chat-btn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
		<svg width="94" height="94" viewBox="0 0 94 94" fill="none" xmlns="http://www.w3.org/2000/svg">
		<g clip-path="url(#clip0_632_20122)">
		<path d="M47 94C72.9574 94 94 72.9574 94 47C94 21.0426 72.9574 0 47 0C21.0426 0 0 21.0426 0 47C0 72.9574 21.0426 94 47 94Z" fill="#89B97C"/>
		<g opacity="0.2">
		<path d="M76.375 47C76.375 32.4594 63.1562 20.5625 47 20.5625C30.8438 20.5625 17.625 32.4594 17.625 47C17.625 61.1 29.8156 72.5563 45.2375 73.2906C46.2656 78.725 47 82.25 47 82.25C47 82.25 54.3437 77.8438 61.1 70.2062C70.2062 65.6531 76.375 56.9875 76.375 47Z" fill="#231F20"/>
		</g>
		<path d="M71.9687 42.3C71.9687 64.3312 47 79.3125 47 79.3125C47 79.3125 33.1937 17.625 47 17.625C60.8062 17.625 71.9687 28.6406 71.9687 42.3Z" fill="white"/>
		<path d="M47 70.5C63.2234 70.5 76.375 58.6635 76.375 44.0625C76.375 29.4615 63.2234 17.625 47 17.625C30.7766 17.625 17.625 29.4615 17.625 44.0625C17.625 58.6635 30.7766 70.5 47 70.5Z" fill="white"/>
		<path d="M47 47C48.6223 47 49.9375 45.6848 49.9375 44.0625C49.9375 42.4402 48.6223 41.125 47 41.125C45.3777 41.125 44.0625 42.4402 44.0625 44.0625C44.0625 45.6848 45.3777 47 47 47Z" fill="#4F5D73"/>
		<path d="M58.75 47C60.3723 47 61.6875 45.6848 61.6875 44.0625C61.6875 42.4402 60.3723 41.125 58.75 41.125C57.1277 41.125 55.8125 42.4402 55.8125 44.0625C55.8125 45.6848 57.1277 47 58.75 47Z" fill="#4F5D73"/>
		<path d="M35.25 47C36.8723 47 38.1875 45.6848 38.1875 44.0625C38.1875 42.4402 36.8723 41.125 35.25 41.125C33.6277 41.125 32.3125 42.4402 32.3125 44.0625C32.3125 45.6848 33.6277 47 35.25 47Z" fill="#4F5D73"/>
		</g>
		<defs>
		<clipPath id="clip0_632_20122">
		<rect width="94" height="94" fill="white"/>
		</clipPath>
		</defs>
		</svg>
	</button>
	<div class="dropdown-menu border-0 p-0">
		<div class="intro-box p-4" id="">
			<div class="d-flex flex-column gap-35">
				<div class="text-center"><img src="<?= THEME_URL."/assets/images/logo-text-gray.svg" ?>" alt="All that Honors Club"></div>
				<div class="intro-content bg-white p-4 text-center">					
					<strong class="d-block mb-2">💬 실시간 채팅 상담 안내</strong>
					<p class="mb-4"> 궁금하신 점이 있으시면 언제든지 문의해주세요! <br>담당자가 순차적으로 확인 후 신속히 답변드리겠습니다.</p>
					<strong class="d-block mb-2">🕒 운영시간</strong>
					<p class="mb-4">월–금 오전 10시 ~ 오후 6시 (주말·공휴일 제외)</p>
					<button class="btn btn-primary fw-medium text-center px-2" type="button" style="width:95px;">문의하기</button>
				</div>
			</div>
		</div>
		<div class="main-chat-box d-flex flex-column position-relative h-100 bg-white d-none">
			<div class="header d-flex flex-column gap-35 flex-shrink-0 p-4 pb-0">
				<div class="d-flex column-gap-3 align-items-center">
					<button class="btn p-0 border-0" type="button">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
						<path d="M15 6L9 12L15 18" stroke="#1C1C1C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</button>
					<img class="logo" src="<?= THEME_URL."/assets/images/logo-text-gray.svg" ?>" alt="All that Honors Club">
				</div>	
				<div>
					<div class="text-center time mb-3"></div>
				</div>
			</div>
			<div class="body px-4">
				<div class="category d-flex column-gap-2 align-items-center d-none" id="category-breadcrumb">
					<span class="main-category"></span>
					<svg width="7" height="7" viewBox="0 0 7 7" fill="none" xmlns="http://www.w3.org/2000/svg" class="arrow-icon d-none">
					<path d="M6.40723 3.78809L0.605469 6.7207V5.54004L4.99805 3.44531V3.36914L0.605469 1.26172V0.0810547L6.40723 3.02637V3.78809Z" fill="#878787"/>
					</svg>
					<span class="sub-category d-none"></span>
				</div>
				<div class="select-category p-3" id="main-category-selection">
					<p>어떤 점이 궁금하세요?</p>
					<div class="list d-flex column-gap-2 row-gap-2 flex-wrap">
						<?php  
						$categories = include get_template_directory() . '/config/livechat_categories.php';
						foreach ($categories['main_categories'] as $mainCategory => $subCategories) { ?>
							<button class="btn category-btn" type="button" data-category="<?= $mainCategory ?>"><?= $mainCategory ?></button>
						<?php
						}
						?>
					</div>
				</div>
				<div class="select-category p-3 d-none" id="sub-category-selection">
					<p>어떤 점이 궁금하세요?</p>
					<div class="list d-flex column-gap-2 row-gap-2 flex-wrap" id="sub-category-list"></div>
				</div>		
				<!-- Messages will be populated by JavaScript -->
				<div class="item typing d-none"></div>
			</div>
			<div class="footer flex-shrink-0 bg-white">
				<div class="d-flex align-items-center">
					<input type="text" class="form-control border-0 w-100 bg-white p-0 h-auto" name="" placeholder="내용을 입력해주세요.">
					<div class="d-flex column-gap-3 align-items-center flex-shrink-0">
						<label>
							<input type="file" class="d-none" name="">
							<svg width="26" height="26" viewBox="0 0 26 26" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M5.3608 22.2958C3.87027 20.8056 3.07798 19.0665 2.9839 17.0784C2.88853 15.0902 3.51064 13.4265 4.85022 12.0873L14.8509 2.08902C15.821 1.11921 17.0335 0.668957 18.4886 0.73825C19.9437 0.807543 21.2048 1.3756 22.2719 2.44242C23.3389 3.50923 23.9071 4.77001 23.9764 6.22474C24.0457 7.67947 23.5954 8.89174 22.6253 9.86155L13.6178 18.8669C13.0019 19.4826 12.2456 19.7734 11.3491 19.7391C10.4518 19.7041 9.66442 19.3479 8.98691 18.6706C8.3094 17.9932 7.95315 17.206 7.91815 16.309C7.88384 15.4126 8.17463 14.6566 8.79053 14.0408L17.7981 5.03547L18.9922 6.22929L9.98464 15.2346C9.66129 15.5579 9.51409 15.9421 9.54305 16.3873C9.57262 16.8319 9.75678 17.2235 10.0955 17.5622C10.4343 17.9009 10.826 18.085 11.2707 18.1146C11.716 18.1435 12.1003 17.9963 12.4237 17.6731L21.4312 8.66773C22.1087 7.9904 22.4243 7.15876 22.378 6.17281C22.3303 5.18679 21.9254 4.31278 21.1632 3.55077C20.418 2.80569 19.5526 2.40968 18.567 2.36275C17.5801 2.31575 16.7479 2.63092 16.0704 3.30824L6.06974 13.3065C5.0381 14.3379 4.55519 15.6254 4.62099 17.1689C4.68611 18.7119 5.29455 20.059 6.44632 21.2105C7.59809 22.362 8.94595 22.9707 10.4899 23.0364C12.0332 23.1015 13.3207 22.6184 14.3523 21.587L24.353 11.5888L25.5725 12.808L15.5718 22.8062C14.2322 24.1455 12.5684 24.7678 10.5805 24.6731C8.59121 24.5784 6.85132 23.786 5.3608 22.2958Z" fill="#2A3547"/>
							</svg>
						</label>
						<button class="btn btn-primary" type="submit">전송</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- Chat Data -->
<script>
window.liveChatConfig = {
    ajaxUrl: '<?= admin_url('admin-ajax.php') ?>',
    homeUrl: '<?= home_url() ?>',
    nonce: '<?= wp_create_nonce('livechat_nonce') ?>',
    themeUrl: '<?= THEME_URL ?>',
    currentUser: <?= json_encode([
        'id' => get_current_user_id(),
        'name' => wp_get_current_user()->display_name ?? '',
        'email' => wp_get_current_user()->user_email ?? ''
    ]) ?>,
    categories: <?= json_encode($categories) ?>
};
</script>