<!-- LiveChat Widget -->
<div class="chat-box dropup">
	<button type="button" class="btn p-0 border-0 open-chat-btn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
		<svg width="70" height="70" viewBox="0 0 70 70" fill="none" xmlns="http://www.w3.org/2000/svg">
		<path d="M35 70C54.33 70 70 54.33 70 35C70 15.67 54.33 0 35 0C15.67 0 0 15.67 0 35C0 54.33 15.67 70 35 70Z" fill="#89B97C"/>
		<g opacity="0.2">
		<path d="M56.875 35C56.875 24.1719 47.0312 15.3125 35 15.3125C22.9688 15.3125 13.125 24.1719 13.125 35C13.125 45.5 22.2031 54.0313 33.6875 54.5781C34.4531 58.625 35 61.25 35 61.25C35 61.25 40.4687 57.9687 45.5 52.2812C52.2812 48.8906 56.875 42.4375 56.875 35Z" fill="#231F20"/>
		</g>
		<path d="M53.5937 31.5C53.5937 47.9062 35 59.0625 35 59.0625C35 59.0625 24.7187 13.125 35 13.125C45.2812 13.125 53.5937 21.3281 53.5937 31.5Z" fill="white"/>
		<path d="M35 52.5C47.0812 52.5 56.875 43.6856 56.875 32.8125C56.875 21.9394 47.0812 13.125 35 13.125C22.9188 13.125 13.125 21.9394 13.125 32.8125C13.125 43.6856 22.9188 52.5 35 52.5Z" fill="white"/>
		<path d="M35 35C36.2081 35 37.1875 34.0206 37.1875 32.8125C37.1875 31.6044 36.2081 30.625 35 30.625C33.7919 30.625 32.8125 31.6044 32.8125 32.8125C32.8125 34.0206 33.7919 35 35 35Z" fill="#4F5D73"/>
		<path d="M43.75 35C44.9581 35 45.9375 34.0206 45.9375 32.8125C45.9375 31.6044 44.9581 30.625 43.75 30.625C42.5419 30.625 41.5625 31.6044 41.5625 32.8125C41.5625 34.0206 42.5419 35 43.75 35Z" fill="#4F5D73"/>
		<path d="M26.25 35C27.4581 35 28.4375 34.0206 28.4375 32.8125C28.4375 31.6044 27.4581 30.625 26.25 30.625C25.0419 30.625 24.0625 31.6044 24.0625 32.8125C24.0625 34.0206 25.0419 35 26.25 35Z" fill="#4F5D73"/>
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