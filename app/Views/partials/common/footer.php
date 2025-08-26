<footer class="main-footer">
	<div class="container">
		<div class="d-flex justify-content-between flex-column flex-md-row column-gap-3">
			<div class="box d-flex flex-column row-gap-3">
				<img src="<?= THEME_URL."/assets/images/logo-text.svg" ?>" alt="All that Honors Club" style="max-width: 225px;">
				<p class="mb-0">상호 : 올댓아너스클럽 <span class="line"></span> 대표자 : 김용순</p>
				<p class="mb-0">주소 : 서울특별시 강서구 마곡중앙6로 11, 보타닉파크 3차, 719호</p>
				<p class="mb-0">사업자 등록번호 : 442-88-02286<span class="line"></span>개인정보보호책임자 : 김용순</p>
				<p class="mb-0">통신판매업신고번호 : 2022-서울강서- 2168호</p>
				<p class="mb-0">전화번호 : 1600-0595 <span class="line"></span> 이메일 :athc@allthathonorsclub.com</p>
			</div>
<?php
// Get policy files for footer
$defaultPolicyTypes = [
    'terms_of_service' => '이용약관',
    'privacy_policy' => '개인정보처리방침',
    'travel_terms' => '국내/외 여행 표준약관'
];

$policyArgs = [
    'post_type' => 'attachment',
    'post_mime_type' => ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
    'post_status' => 'inherit',
    'numberposts' => -1,
    'meta_query' => [
        [
            'key' => '_policy_type',
            'compare' => 'EXISTS'
        ]
    ]
];

$policyAttachments = get_posts($policyArgs);
$footerPolicies = [];

foreach ($policyAttachments as $attachment) {
    $policyTypes = get_post_meta($attachment->ID, '_policy_type', false);
    $fileUrl = wp_get_attachment_url($attachment->ID);
    
    foreach ($policyTypes as $policyType) {
        if (isset($defaultPolicyTypes[$policyType])) {
            $footerPolicies[$policyType] = [
                'name' => $defaultPolicyTypes[$policyType],
                'url' => $fileUrl
            ];
        }
    }
}
?>
			<div class="box d-flex flex-column" style="row-gap:20px;">
				<?php foreach ($defaultPolicyTypes as $typeKey => $typeName): ?>
					<?php if (isset($footerPolicies[$typeKey])): ?>
						<a href="<?= $footerPolicies[$typeKey]['url'] ?>" download><?= $typeName ?></a>
					<?php else: ?>
						<a href="#" onclick="alert('<?= $typeName ?> 파일이 아직 업로드되지 않았습니다.');"><?= $typeName ?></a>
					<?php endif; ?>
				<?php endforeach; ?>
			</div>
			<div class="box d-flex flex-column row-gap-3">
				<p class="mb-0 text-white fw-bolder">고객센터</p>
				<strong>1600-0595</strong>
				<p class="mb-0">평일 오전 10시 ~ 오후 5시</p>
				<p class="mb-0"> © (주) 올댓아너스클럽 All Right Reserved.</p>
			</div>
		</div>
	</div>
</footer>
<nav class="bottom-nav d-flex align-items-center">
	<div class="container">
		<ul class="list-unstyled mb-0">
			<li><a href="#">회사 및 서비스 소개</a></li>
			<li><a href="#">멤버십</a></li>
			<li><a href="#">1문의</a></li>
		</ul>
	</div>
</nav>
<div class="chat-box dropup">
	<button type="button" class="btn p-0 border-0" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
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
	<div class="dropdown-menu border-0">
		<div class="intro-box">
			<div class="d-flex flex-column gap-35">
				<div class="text-center"><img src="<?= THEME_URL."/assets/images/logo-text-gray.svg" ?>" alt="All that Honors Club"></div>
				<div class="intro-content">					
					<strong class="d-block mb-2">💬 실시간 채팅 상담 안내</strong>
					<p class="mb-4"> 궁금하신 점이 있으시면 언제든지 문의해주세요! <br>담당자가 순차적으로 확인 후 신속히 답변드리겠습니다.</p>
					<strong class="d-block mb-2">🕒 운영시간</strong>
					<p class="mb-4">월–금 오전 10시 ~ 오후 6시 (주말·공휴일 제외)</p>
					<button class="btn btn-primary" type="button">문의하기</button>
				</div>
			</div>
		</div>
	</div>
</div>