<?php 
get_header(); ?>
<section class="home-section mem-r-1 home-r1">
	<div class="container">
		<div class="d-xl-flex">
			<div class="left d-flex align-items-center justify-content-center w-100">
				<div class="box align-items-center d-flex flex-column text-center">
					<p class="mb-0 text-uppercase">our membership</p>
					<strong class="text-nowrap fw-normal">The Heritage<br>Travel Club</strong>
					<p class="mb-0 text-center">
						Vivamus volutpat eros pulvinar velit laoreet, sit amet egestas erat dignissim. Sed quis rutrum tellus, sit amet viverra felis. Cras sagittis sem sit amet urna feugiat rutrum. Nam nulla ipsum, venenatis malesuada felis.
					</p>
				</div>
			</div>		
			<div class="right d-md-flex column-gap-4">
				<div class="item">
					<p class="title">SIGNATURE</p>
					<p class="description mb-2">전 여정 엄선된 맞춤형 서비스를 제공</p>
					<div class="price-box d-flex align-items-end column-gap-2 mb-32">
						<strong>10,000,000원</strong>
						<span>세금 별도</span>
					</div>
					<img src="<?= THEME_URL."/assets/images/signature-card.png" ?>" alt="All that Honors Club" class="mb-32">
					<div class="group-action-btn mb-32 d-flex column-gap-2">
						<a href="#" class="btn btn-outline-primary fw-medium">Learn More</a>
						<button class="btn btn-primary fw-medium" type="button">Contact</button>
					</div>
					<ul class="list-unstyled mb-0 d-flex flex-column row-gap-3">
						<li>
							<strong>BENEFIT 1</strong>
							<p>tVivamus volutpat eros pulvinar v</p>
						</li>
						<li>
							<strong>BENEFIT 2</strong>
							<p>tVivamus volutpat eros pulvinar v</p>
						</li>
						<li>
							<strong>BENEFIT 3</strong>
							<p>tVivamus volutpat eros pulvinar v</p>
						</li>
					</ul>
				</div>
				<div class="item">
					<p class="title">PRIME</p>
					<p class="description mb-2">핵심 혜택 중심의 실속형 패키지</p>
					<div class="price-box d-flex align-items-end column-gap-2 mb-32">
						<strong>5,000,000원</strong>
						<span>세금 별도</span>
					</div>
					<img src="<?= THEME_URL."/assets/images/prime-card.png" ?>" alt="All that Honors Club" class="mb-32">
					<div class="group-action-btn mb-32 d-flex column-gap-2">
						<a href="#" class="btn btn-outline-primary fw-medium">Learn More</a>
						<button class="btn btn-primary fw-medium" type="button">Contact</button>
					</div>
					<ul class="list-unstyled mb-0 d-flex flex-column row-gap-3">
						<li>
							<strong>BENEFIT 1</strong>
							<p>tVivamus volutpat eros pulvinar v tVivamus volutpat eros pulvinar v  tVivamus volutpat eros pulvinar v</p>
						</li>
						<li>
							<strong>BENEFIT 2</strong>
							<p>tVivamus volutpat eros pulvinar v</p>
						</li>
						<li>
							<strong>BENEFIT 3</strong>
							<p>tVivamus volutpat eros pulvinar v</p>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>
<?php  

ob_start(); ?>
<div class="list">
	<?php  
	foreach (["Travel<br>Care","special<br>benefit","lifestyle<br>care","welcome<br>gift"] as $key => $value) { ?>
		<div class="item">
			<div class="left"><?= $value ?></div>
			<div class="right">
				<strong>tVivamus volutpat eros pulvinar v</strong>
				<strong>tVivamus volutpat eros pulvinar v</strong>
				<strong>tVivamus volutpat eros pulvinar v</strong>
			</div>
		</div>
	<?php
	}
	?>
	
</div>
<?php
$tab_content_0 = ob_get_contents();
ob_end_clean();

ob_start(); ?>
<div class="list">
	<div class="item">
		<div class="left">
			제공 혜택
		</div>
		<div class="right">
			<div>
				<p>tVivamus volutpat eros pulvinar v</p>
				<span>해당 혜택에 대한 부가 설명입니다.</span>
			</div>
			<div>
				<p>tVivamus volutpat eros pulvinar v</p>
				<span>해당 혜택에 대한 부가 설명입니다.</span>
			</div>
			<div>
				<p>tVivamus volutpat eros pulvinar v</p>
				<span>해당 혜택에 대한 부가 설명입니다.</span>
			</div>
		</div>
	</div>
	<div class="item">
		<div class="left">이용안내</div>
		<div class="right">
			<ul>
				<li>tVivamus volutpat eros pulvinar v</li>
				<li>tVivamus volutpat eros pulvinar v</li>
				<li>tVivamus volutpat eros pulvinar v</li>
			</ul>
		</div>
	</div>
</div>
<?php
$tab_content_1 = ob_get_contents();
ob_end_clean();

function render_tab($prefix,$tab_content_0,$tab_content_1){
	$tab = $nav = "";	 	
  	$tab_content_2 = $tab_content_3 = $tab_content_4 = "";
  	$arr = [
		["icon-tab-1.svg","혜택 <br class='d-lg-none'>요약"],
		["icon-tab-2.svg","Travel <br class='d-lg-none'>Care"],
		["icon-tab-3.svg","special <br class='d-lg-none'>benefit"],
		["icon-tab-4.svg","lifestyle <br class='d-lg-none'>care"],
		["icon-tab-5.svg","welcome <br class='d-lg-none'>gift"]
	];
	foreach ($arr as $key => $value) {
		if($key == 0){
			$nav_class = " active";
			$tab_class = " show active";
		}else{
			$nav_class = $tab_class = "";
		}
		$nav .= '<li class="nav-item" ><button class="nav-link'.$nav_class.'" data-bs-toggle="tab" data-bs-target="#card-tab-'.$key.$prefix.'" type="button" role="tab">'.file_get_contents(THEME_URL."/assets/images/".$value[0]).'<span>'.$value[1].'</span></button></li>';
		$tab .= '<div class="tab-pane fade'.$tab_class.'" id="card-tab-'.$key.$prefix.'" role="tabpanel">'.${"tab_content_" . $key}.'</div>';
	}
	return ["tab" => $tab,"nav" => $nav];
}; //end func
?>
<section class="home-section bg-gold">
	<div class="container card-detail">
		<div class="card-infor mb-64">
			<img src="<?= THEME_URL."/assets/images/signature-card-detail.png" ?>" alt="All that Honors Club">
			<div class="text">
				<span class="d-block fs-14 lh-14 lp-308 mb-3">The Heritage Travel Club</span>
				<p class="card-name mb-3">Signature</p>
				<p class="mb-32">전담 매니저를 상시 배정하여 전 여정 엄선된 맞춤형 서비스를 제공</p>
				<hr class="mt-0 mb-32">
				<ul class="mb-0 list-unstyled d-flex flex-column row-gap-2">
					<li>
						<strong>카드 연회비</strong>
						<span>10,000,000원 (세금 별도)</span>
					</li>
					<li>
						<strong>이용 기간</strong>
						<span>가입 후 서비스 시작일로부터 1년</span>
					</li>
					<li>
						<strong>가입문의</strong>
						<span>02-1234-5678 혹은 웹사이트 1:1 문의</span>
					</li>
				</ul>
			</div>
		</div>
		<?php $html = render_tab("_1",$tab_content_0,$tab_content_1) ?>
		<ul class="nav nav-tabs flex-nowrap" role="tablist"><?= $html["nav"] ?></ul>
		<div class="tab-content mb-64"><?= $html["tab"] ?></div>
		<div class="note-box mb-64">
			<strong class="title">유의사항</strong>
			<ul class="mb-0">
				<li>tVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtViva</li>
				<li>tVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtViva nar vtVivamus volutpat eros pulvinar vtViva</li>
				<li>tVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtViva ar vtVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtViva</li>
			</ul>
		</div>
		<div class="text-center"><a href="#" class="btn btn-dark btn-lg fw-medium">Contact</a></div>
	</div>
</section>
<section class="home-section">
	<div class="container card-detail">
		<div class="card-infor mb-64">
			<img src="<?= THEME_URL."/assets/images/prime-card-detail.png" ?>" alt="All that Honors Club">
			<div class="text">
				<span class="d-block fs-14 lh-14 lp-308 mb-3">The Heritage Travel Club</span>
				<p class="card-name mb-3">Prime</p>
				<p class="mb-32">핵심 혜택 중심의 실속형 패키지를 제공</p>
				<hr class="mt-0 mb-32">
				<ul class="mb-0 list-unstyled d-flex flex-column row-gap-2">
					<li>
						<strong>카드 연회비</strong>
						<span>5,000,000원 (세금 별도)</span>
					</li>
					<li>
						<strong>이용 기간</strong>
						<span>가입 후 서비스 시작일로부터 1년</span>
					</li>
					<li>
						<strong>가입문의</strong>
						<span>02-1234-5678 혹은 웹사이트 1:1 문의</span>
					</li>
				</ul>
			</div>
		</div>
		<?php $html = render_tab("_2",$tab_content_0,$tab_content_1) ?>
		<ul class="nav nav-tabs flex-nowrap" role="tablist"><?= $html["nav"] ?></ul>
		<div class="tab-content mb-64"><?= $html["tab"] ?></div>
		<div class="note-box mb-64">
			<strong class="title">유의사항</strong>
			<ul class="mb-0">
				<li>tVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtViva</li>
				<li>tVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtViva nar vtVivamus volutpat eros pulvinar vtViva</li>
				<li>tVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtViva ar vtVivamus volutpat eros pulvinar vtVivamus volutpat eros pulvinar vtViva</li>
			</ul>
		</div>
		<div class="text-center"><a href="#" class="btn btn-dark btn-lg fw-medium">Contact</a></div>
	</div>
</section>
<script>
	jQuery(document).ready(function($){
		$("nav.main-nav li:nth-child(2) a").addClass("active");
	})
</script>
<?php
get_footer(); 
?>