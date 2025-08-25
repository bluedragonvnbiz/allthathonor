<?php
// Kiểm tra data và exposure status
$bannerData = $banner ?? [];
$sectionInfo = $bannerData['section_info'] ?? [];
$contentInfo = $bannerData['content_info'] ?? [];
$exposureStatus = $sectionInfo['section_exposure_status'] ?? 'hide';

if ($exposureStatus === 'expose'):
?>

<div class="home-banner d-flex align-items-center justify-content-center" style="background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(<?= htmlspecialchars($sectionInfo['section_background_image'] ?? '') ?>) no-repeat center;background-size: cover;">
	<div class="text-box text-center">
		<?php if (!empty($contentInfo['content_top_phrase'])): ?>
			<span class="fs-14 text-uppercase"><?= htmlspecialchars($contentInfo['content_top_phrase']) ?></span>
		<?php endif; ?>
		
		<?php if (!empty($contentInfo['content_main_title'])): ?>
			<strong><?= htmlspecialchars($contentInfo['content_main_title']) ?></strong>
		<?php endif; ?>
		
		<?php if (!empty($contentInfo['content_description'])): ?>
			<span><?= nl2br(htmlspecialchars($contentInfo['content_description'])) ?></span>
		<?php endif; ?>
	</div>
</div>

<?php endif; ?>