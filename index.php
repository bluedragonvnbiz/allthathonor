<?php 
get_header(); ?>
<div class="home-banner d-flex align-items-center justify-content-center">
	<div class="text-box text-center">
		<span class="fs-14 text-uppercase">premium service, tailored for you</span>
		<strong>ALL THAT HONORS CLUB</strong>
		<span>대한민국 최고 항공사와 호텔 출신 전문가들이 오직 당신만을 위한 퍼스트클래스 품격을 완성합니다. </span>
	</div>
</div>
<section class="home-section container home-r1">
	<div class="d-flex">
		<div class="left d-flex align-items-center justify-content-end w-100">
			<div class="box align-items-center d-flex flex-column">
				<p class="mb-0 text-uppercase">our membership</p>
				<strong class="text-nowrap fw-normal">The Heritage Travel Club</strong>
				<p class="mb-0 text-center">
					올댓아너스의 Excutive Travel Care Annual Membership은<br>
				 	국내 최고 항공사·호텔 출신 전문가들이 선사하는<br>퍼스트클래스 맞춤형 서비스를 위한 프라이빗 멤버십입니다.<br>
					단순한 혜택을 넘어, 당신만을 위한 하이엔드 서비스로 완성됩니다.
				</p>
				<a href="#" class="btn btn-primary text-uppercase">view detail</a>
			</div>
		</div>
		<div class="gallery flex-shrink-0">
			<div class="images position-relative type-1">
				<img id="first" src="<?= THEME_URL."/assets/images/demo/home-1.jpg" ?>" alt="">				
				<img id="second" src="<?= THEME_URL."/assets/images/demo/home-2.jpg" ?>" alt="">				
				<img id="third" src="<?= THEME_URL."/assets/images/demo/home-3.jpg" ?>" alt="">				
			</div>	
		</div>
	</div>
</section>
<section class="home-r2 d-flex">
	<div class="left flex-shrink-0">
	<?php  
	for ($i=1; $i < 5; $i++) { ?>
		<div class="" style="background-image: url('<?= THEME_URL."/assets/images/demo/demo-".$i.".jpg" ?>');"></div>
	<?php
	}
	?>
	</div>
	<div class="right w-100">
		<div class="box d-flex flex-column row-gap-35">
			<div class="top d-flex flex-column row-gap-35">
				<p class="mb-0 text-uppercase lp-308 lh-14 text-white">company introduction</p>
				<h4 class="fw-normal mb-0 text-white">All That <span>Honors Club</span></h4>
				<p class="mb-0 fw-medium lp-168 lh-27">올댓아너스클럽은 국내 최초의 투어 컨시어지 전문 기업으로 <br>국내 고객과 해외 관광객을 대상으로 좀 더 편리하고 품격 높은 맞춤형 여행을 제공합니다.</p>
			</div>
			<div class="bottom d-flex column-gap-35">
				<div class="w-100">
					<p class="mb-2 lp-168 text-white">Our Aspiration</p>
					<p class="mb-0 lp-168 fw-light lh-27">올댓아너스클럽은 고객과 함께 사회적 가치를 실현하며, 성과를 선하게 환원하는 신뢰받는 기업으로 성장할 것을 약속드립니다.</p>
				</div>
				<div class="w-100">
					<p class="mb-2 lp-168 text-white">Our Commitment</p>
					<p class="mb-0 lp-168 fw-light lh-27">대한민국 최고 수준의 항공사·호텔리어 출신 전문가들이 퍼스트 클래스의 품격과 세심함으로, 고객의 건강과 활력, 생활의 편리함을 더해드립니다.</p>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="home-section home-r3">
	<div class="container">
		<div class="title-box">
			<p class="sub-title">our Service</p>
			<h3 class="title">Our Services</h3>
		</div>
		<div class="products">
			<div class="item">
				<div class="product">
					<img src="<?= THEME_URL."/assets/images/demo/product-1.jpg" ?>" alt="">
					<div class="infor">
						<p class="cat">Honors red carpet</p>
						<h4 class="title">공항 의전 서비스 (레드카펫)</h4>
						<p class="description">상세설명 내용입니다. 상세설명 내용입니다. 상세설명 내용입니다. 상세설명 내용입니다. 상세설명 내용입니다. </p>
						<a href="#" class="btn btn-outline-primary">view detail</a>
					</div>
				</div>
				<div class="more-infor bg-black text-white">
					<p class="title">Gidik Dinneg</p>
					<p class="description">Phasellus enim libero, blandit vel sapien vitae, condimentum ultricies magna et.</p>
				</div>
			</div>
			<div class="item">
				<div class="more-infor bg-gold text-white">
					<p class="title">Gidik Dinneg</p>
					<p class="description">Phasellus enim libero, blandit vel sapien vitae, condimentum ultricies magna et.</p>
				</div>
				<div class="product">
					<img src="<?= THEME_URL."/assets/images/demo/product-2.jpg" ?>" alt="">
					<div class="infor">
						<p class="cat">VIP Chauffeur Service</p>
						<h4 class="title">VIP 차량 서비스</h4>
						<p class="description">Phasellus enim libero, blandit vel sapien vitae, condimentum ultricies magna et.Quisque euismod orci ut et lobortis.</p>
						<a href="#" class="btn btn-outline-primary">view detail</a>
					</div>
				</div>				
			</div>
			<div class="item">
				<div class="product">
					<img src="<?= THEME_URL."/assets/images/demo/product-1.jpg" ?>" alt="">
					<div class="infor">
						<p class="cat">Honors Blue</p>
						<h4 class="title">공항 의전 서비스 (블루)</h4>
						<p class="description">상세설명 내용입니다. 상세설명 내용입니다. 상세설명 내용입니다. 상세설명 내용입니다. 상세설명 내용입니다. </p>
						<a href="#" class="btn btn-outline-primary">view detail</a>
					</div>
				</div>
				<div class="more-infor bg-black text-white">
					<p class="title">Gidik Dinneg</p>
					<p class="description">Phasellus enim libero, blandit vel sapien vitae, condimentum ultricies magna et.</p>
				</div>
			</div>
		</div>
	</div>
</section>
<section class="home-section home-r4">
	<div class="container">
		<div class="title-box">
			<p class="sub-title">our Service</p>
			<h3 class="title">Benefit/Voucher</h3>
		</div>
		<div class="content-box">
			<div class="row-item d-flex">
				<div class="w-50 d-flex align-items-center justify-content-center bg-white">
					<div class="text-box">
						<p class="title">wellness</p>
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
			<div class="row-item d-flex">
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
<script>
	jQuery(document).ready(function($){
		$("nav.main-nav li:first-child a").addClass("active");
		$('.home-r4 .row-item').each(function () {
	        let $carousel = $(this);
	        let swiperEl = $carousel.find('.swiper')[0]; 
	        let bulletPaginationEl = $carousel.find('.pv-swiper-pagination')[0]; 
	        var swiper = new Swiper(swiperEl, {
	            loop: true,
	            slidesPerView: 1,
	            spaceBetween: 0,
	            pagination: {
	                el: bulletPaginationEl,
	            },
	        });

	    });

	    var currentIndex = 1;
		var totalSlides = 3;
		var autoPlayDelay = 3000; 
		var reverse = true; // true = cung chieu, false = nguoc chieu
		var $image = $(".gallery .images");

		setInterval(function(){
		    if (reverse) {
		        currentIndex--;
		        if (currentIndex < 1) currentIndex = totalSlides;
		    } else {
		        currentIndex++;
		        if (currentIndex > totalSlides) currentIndex = 1;
		    }
		    $image.removeClass(function (index, className) {
		        return (className.match(/(^|\s)type-\d+/g) || []).join(' ');
		    });
		    $image.addClass("type-" + currentIndex);
		}, autoPlayDelay);
	});//end ready
</script>
<?php
get_footer(); 
?>