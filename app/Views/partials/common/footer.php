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