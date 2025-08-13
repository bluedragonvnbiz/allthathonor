<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=3.0, user-scalable=no">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<header class="main-header py-3">
	<div class="container">
		<div class="d-flex justify-content-between align-items-center">
			<a href="/"><img src="<?= THEME_URL."/assets/images/logo.svg" ?>" alt="All that Honors Club"></a>
			<nav class="main-nav">
				<ul class="list-unstyled mb-0">
					<li><a href="#">회사 및 서비스 소개</a></li>
					<li><a href="#">멤버십</a></li>
					<li><a href="#">고객 문의</a></li>
				</ul>
			</nav>			
		</div>
	</div>
</header>