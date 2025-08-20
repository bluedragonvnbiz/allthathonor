<?php
$companyIntroData = $company_intro ?? [];

$sectionInfo = $companyIntroData['section_info'] ?? [];
$contentInfo = $companyIntroData['content_info'] ?? [];

$exposureStatus = $sectionInfo['section_exposure_status'] ?? 'hide';
if ($exposureStatus === 'expose'):
?>
<section class="home-r2 d-lg-flex">
	<div class="left flex-shrink-0">
	<?php  
	// Loop through 4 images from database
	for ($i=1; $i <= 4; $i++) { 
		$imageKey = "section_image_{$i}";
		$imageUrl = $sectionInfo[$imageKey] ?? '';
		
		if (!empty($imageUrl)):
	?>
		<div class="" style="background-image: url('<?= htmlspecialchars($imageUrl) ?>');"></div>
	<?php 
		endif;
	}
	?>
	</div>
	<div class="right w-100">
		<div class="box d-flex flex-column row-gap-35">
			<div class="top d-flex flex-column row-gap-35">
				<?php if (!empty($contentInfo['content_top_phrase'])): ?>
					<p class="mb-0 text-uppercase lp-308 lh-14 text-white"><?= htmlspecialchars($contentInfo['content_top_phrase']) ?></p>
				<?php endif; ?>
				
				<?php if (!empty($contentInfo['content_main_title'])): ?>
					<h4 class="fw-normal mb-0 text-white">
						<?php if (is_array($contentInfo['content_main_title'])): ?>
							<?= htmlspecialchars($contentInfo['content_main_title']['part1'] ?? '') ?> 
							<span><?= htmlspecialchars($contentInfo['content_main_title']['part2'] ?? '') ?></span>
						<?php else: ?>
							<?= htmlspecialchars($contentInfo['content_main_title']) ?>
						<?php endif; ?>
					</h4>
				<?php endif; ?>
				
				<?php if (!empty($contentInfo['content_description'])): ?>
					<p class="mb-0 fw-medium lp-168 lh-27"><?= nl2br(htmlspecialchars($contentInfo['content_description'])) ?></p>
				<?php endif; ?>
			</div>
			<div class="bottom d-flex gap-35 flex-lg-row flex-column">
				<div class="w-100">
					<?php if (!empty($contentInfo['content_title_1'])): ?>
						<p class="mb-2 lp-168 text-white"><?= htmlspecialchars($contentInfo['content_title_1']) ?></p>
					<?php endif; ?>
					
					<?php if (!empty($contentInfo['content_description_1'])): ?>
						<p class="mb-0 lp-168 fw-light lh-27"><?= htmlspecialchars($contentInfo['content_description_1']) ?></p>
					<?php endif; ?>
				</div>
				<div class="w-100">
					<?php if (!empty($contentInfo['content_title_2'])): ?>
						<p class="mb-2 lp-168 text-white"><?= htmlspecialchars($contentInfo['content_title_2']) ?></p>
					<?php endif; ?>
					
					<?php if (!empty($contentInfo['content_description_2'])): ?>
						<p class="mb-0 lp-168 fw-light lh-27"><?= htmlspecialchars($contentInfo['content_description_2']) ?></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</section>

<?php endif; ?>