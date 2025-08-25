<?php
$servicesData = $service ?? [];
$sectionInfo = $servicesData['section_info'] ?? [];
$contentInfo = $servicesData['content_info'] ?? [];

$exposureStatus = $sectionInfo['section_exposure_status'] ?? 'hide';

if ($exposureStatus === 'expose'): 

// Lấy product data cho từng service
$productService = new \App\Services\ProductService();
$services = [];

for ($i = 1; $i <= 3; $i++) {
    $productCode = $contentInfo["content_service_{$i}_code"] ?? '';
    if (!empty($productCode)) {
        try {
            $product = $productService->getProduct($productCode);
            if ($product) {
                $services[$i] = [
                    'product' => $product,
                    'title' => $contentInfo["content_service_{$i}_title"] ?? '',
                    'description' => $contentInfo["content_service_{$i}_description"] ?? ''
                ];
            }
        } catch (\Exception $e) {
            error_log("Error loading product {$productCode}: " . $e->getMessage());
        }
    }
}
?>

<section class="home-section home-r3">
	<div class="container">
		<div class="title-box">
			<?php if (!empty($contentInfo['content_top_phrase'])): ?>
				<p class="sub-title"><?= htmlspecialchars($contentInfo['content_top_phrase']) ?></p>
			<?php endif; ?>
			
			<?php if (!empty($contentInfo['content_main_title'])): ?>
				<h3 class="title"><?= htmlspecialchars($contentInfo['content_main_title']) ?></h3>
			<?php endif; ?>
		</div>
		
		<div class="products flex-column flex-md-row">
			<?php 
			$index = 0;
			foreach ($services as $service): 
				$index++;
			?>
				<div class="item">
					<?php if ($index == 1): ?>
						<!-- Service 1: product trước, more-info sau với bg-black -->
						<div class="product">
							<?php if (!empty($service['product']['main_image'])): ?>
								<img src="<?= htmlspecialchars($service['product']['main_image']) ?>" alt="<?= htmlspecialchars($service['product']['product_name'] ?? '') ?>">
							<?php endif; ?>
							<div class="infor">
								<p class="cat"><?= htmlspecialchars($service['product']['product_name_en'] ?? '') ?></p>
								<h4 class="title"><?= htmlspecialchars($service['product']['product_name'] ?? '') ?></h4>
								<p class="description"><?= htmlspecialchars($service['product']['summary_description'] ?? '') ?></p>
								<a href="<?= home_url('/product/view/' . $service['product']['id']) ?>" class="btn btn-outline-primary">view detail</a>
							</div>
						</div>
						<div class="more-infor bg-black text-white">
							<p class="title"><?= htmlspecialchars($service['title']) ?></p>
							<p class="description"><?= nl2br(htmlspecialchars($service['description'])) ?></p>
						</div>
						
					<?php elseif ($index == 2): ?>
						<!-- Service 2: more-info trước, product sau với bg-gold -->
						<div class="more-infor bg-gold text-white">
							<p class="title"><?= htmlspecialchars($service['title']) ?></p>
							<p class="description"><?= nl2br(htmlspecialchars($service['description'])) ?></p>
						</div>
						<div class="product">
							<?php if (!empty($service['product']['main_image'])): ?>
								<img src="<?= htmlspecialchars($service['product']['main_image']) ?>" alt="<?= htmlspecialchars($service['product']['product_name'] ?? '') ?>">
							<?php endif; ?>
							<div class="infor">
								<p class="cat"><?= htmlspecialchars($service['product']['product_name_en'] ?? '') ?></p>
								<h4 class="title"><?= htmlspecialchars($service['product']['product_name'] ?? '') ?></h4>
								<p class="description"><?= htmlspecialchars($service['product']['summary_description'] ?? '') ?></p>
								<a href="<?= home_url('/product/view/' . $service['product']['id']) ?>" class="btn btn-outline-primary">view detail</a>
							</div>
						</div>
						
					<?php elseif ($index == 3): ?>
						<!-- Service 3: product trước, more-info sau với bg-black -->
						<div class="product">
							<?php if (!empty($service['product']['main_image'])): ?>
								<img src="<?= htmlspecialchars($service['product']['main_image']) ?>" alt="<?= htmlspecialchars($service['product']['product_name'] ?? '') ?>">
							<?php endif; ?>
							<div class="infor">
								<p class="cat"><?= htmlspecialchars($service['product']['product_name'] ?? '') ?></p>
								<h4 class="title"><?= htmlspecialchars($service['product']['product_name'] ?? '') ?></h4>
								<p class="description"><?= htmlspecialchars($service['product']['summary_description'] ?? '') ?></p>
								<a href="<?= home_url('/product/view/' . $service['product']['id']) ?>" class="btn btn-outline-primary">view detail</a>
							</div>
						</div>
						<div class="more-infor bg-gold text-white">
							<p class="title"><?= htmlspecialchars($service['title']) ?></p>
							<p class="description"><?= nl2br(htmlspecialchars($service['description'])) ?></p>
						</div>
					<?php endif; ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<?php endif; ?>