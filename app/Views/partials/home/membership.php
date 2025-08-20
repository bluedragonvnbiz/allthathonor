<?php
$membershipData = $membership ?? [];
$sectionInfo = $membershipData['section_info'] ?? [];
$contentInfo = $membershipData['content_info'] ?? [];

$exposureStatus = $sectionInfo['section_exposure_status'] ?? 'hide';

if ($exposureStatus === 'expose'):
?>

<section class="home-section container home-r1">
	<div class="d-lg-flex" style="column-gap:120px">
		<div class="left d-flex align-items-center justify-content-center justify-content-lg-end w-100">
			<div class="box align-items-center d-flex flex-column">
				<?php if (!empty($contentInfo['content_top_phrase'])): ?>
					<p class="mb-0 text-uppercase"><?= htmlspecialchars($contentInfo['content_top_phrase']) ?></p>
				<?php endif; ?>
				
				<?php if (!empty($contentInfo['content_main_title'])): ?>
					<strong class="text-nowrap fw-normal"><?= htmlspecialchars($contentInfo['content_main_title']) ?></strong>
				<?php endif; ?>
				
				<?php if (!empty($contentInfo['content_description'])): ?>
					<p class="mb-0 text-center">
						<?= nl2br(htmlspecialchars($contentInfo['content_description'])) ?>
					</p>
				<?php endif; ?>
				
				<a href="/membership" class="btn btn-primary text-uppercase">view detail</a>
			</div>
		</div>
		<div class="gallery flex-shrink-0">
			<div class="images position-relative type-1">
				<?php if (!empty($sectionInfo['section_image_1'])): ?>
					<img id="first" src="<?= htmlspecialchars($sectionInfo['section_image_1']) ?>" alt="Membership Image 1">
				<?php endif; ?>
				
				<?php if (!empty($sectionInfo['section_image_2'])): ?>
					<img id="second" src="<?= htmlspecialchars($sectionInfo['section_image_2']) ?>" alt="Membership Image 2">
				<?php endif; ?>
				
				<?php if (!empty($sectionInfo['section_image_3'])): ?>
					<img id="third" src="<?= htmlspecialchars($sectionInfo['section_image_3']) ?>" alt="Membership Image 3">
				<?php endif; ?>
			</div>	
		</div>
	</div>
</section>

<?php endif; ?>