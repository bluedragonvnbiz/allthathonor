<?php
$voucherData = $voucher ?? [];
$sectionInfo = $voucherData['section_info'] ?? [];
$contentInfo = $voucherData['content_info'] ?? [];

$exposureStatus = $sectionInfo['section_exposure_status'] ?? 'hide';

// Array
// (
//     [section_location] => 메인
//     [section_section_number] => 5
//     [section_info] => Array
//         (
//             [section_exposure_status] => expose
//         )

//     [content_info] => Array
//         (
//             [content_top_phrase] => our service
//             [content_main_title] => BENEFIT/VOUCHER
//             [content_voucher_1_image] => http://localhost:8000/wp-content/uploads/2025/08/voucher-1.jpg
//             [content_voucher_1_title] => Healthcare and DNA
//             [content_voucher_2_image] => http://localhost:8000/wp-content/uploads/2025/08/voucher-2.jpg
//             [content_voucher_2_title] => VIP INVITATION
//         )

//     [block] => section_info
// )

if ($exposureStatus === 'expose'): ?>
<section class="home-section home-r4">
	<div class="container">
		<div class="title-box">
			<?php if (!empty($contentInfo['content_top_phrase'])): ?>
				<p class="sub-title"><?= htmlspecialchars($contentInfo['content_top_phrase']) ?></p>
			<?php endif; ?>
			
			<?php if (!empty($contentInfo['content_main_title'])): ?>
				<h3 class="title"><?= htmlspecialchars($contentInfo['content_main_title']) ?></h3>
			<?php endif; ?>
		</div>
		<div class="content-box">
			<div class="row-item d-flex flex-column flex-md-row">
				<div class="w-50 d-flex align-items-center justify-content-center bg-white">
					<div class="text-box">
						<p class="title">Voucher</p>
						<div class="swiper">
							<div class="swiper-wrapper">
							<?php  
							for ($i=0; $i < 3; $i++) { ?>
								<div class="swiper-slide">
									<div class="box">
										<p class="slide-title">Health Care & DNA</p>
										<p class="slide-description">Phasellus enim libero, blandit vel sapien vitae, condimentum ultricies magna et. Quisque euismod orci ut et lobortis. Phasellus enim libero, blandit.</p>
										<a href="#" class="btn btn-outline-primary">view detail</a>
									</div>
								</div>
							<?php
							}
							?>
							</div>
						</div>
						<div class="pv-swiper-pagination d-flex align-items-center justify-content-center column-gap-3"></div>
					</div>
				</div>
				<img src="<?= THEME_URL."/assets/images/demo/voucher-1.jpg" ?>" class="w-50 object-fit-cover">
			</div>
			<div class="row-item d-flex flex-column-reverse flex-md-row">
				<img src="<?= THEME_URL."/assets/images/demo/voucher-2.jpg" ?>" class="w-50 object-fit-cover">
				<div class="w-50 d-flex align-items-center justify-content-center bg-white">
					<div class="text-box">
						<p class="title">Event Invitation</p>
						<div class="swiper">
							<div class="swiper-wrapper">
							<?php  
							for ($i=0; $i < 3; $i++) { ?>
								<div class="swiper-slide">
									<div class="box">
										<p class="slide-title">VIP Invitation</p>
										<p class="slide-description">Phasellus enim libero, blandit vel sapien vitae, condimentum ultricies magna et. Quisque euismod orci ut et lobortis. Phasellus enim libero, blandit.</p>
										<a href="#" class="btn btn-outline-primary">view detail</a>
									</div>
								</div>
							<?php
							}
							?>
							</div>
						</div>
						<div class="pv-swiper-pagination d-flex align-items-center justify-content-center column-gap-3"></div>
					</div>
				</div>				
			</div>
		</div>
	</div>
</section>

<?php endif; ?>