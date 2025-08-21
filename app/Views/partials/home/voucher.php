<?php
use App\Services\VoucherService;
$voucherService = new VoucherService();

$voucherData = $voucher ?? [];
$sectionInfo = $voucherData['section_info'] ?? [];
$contentInfo = $voucherData['content_info'] ?? [];

$exposureStatus = $sectionInfo['section_exposure_status'] ?? 'hide';

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
								$voucherId = $contentInfo['content_voucher_1_title_id'] ?? 0;
								if ($voucherId):
									$voucher_info = $voucherService->getVoucher($voucherId);
									?>
									<div class="swiper-slide">
										<div class="box">
											<p class="slide-title"><?= $voucher_info['name'] ?></p>
											<p class="slide-description"><?= $voucher_info['short_description'] ?></p>
											<a href="#" class="btn btn-outline-primary">view detail</a>
										</div>
									</div>
								<?php endif; ?>
							</div>
						</div>
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
								$voucherId = $contentInfo['content_voucher_2_title_id'] ?? 0;
								if ($voucherId):
									$voucher_info = $voucherService->getVoucher($voucherId);
									?>
									<div class="swiper-slide">
										<div class="box">
											<p class="slide-title"><?= $voucher_info['name'] ?></p>
											<p class="slide-description"><?= $voucher_info['short_description'] ?></p>
											<a href="#" class="btn btn-outline-primary">view detail</a>
										</div>
									</div>
								<?php endif; ?>
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