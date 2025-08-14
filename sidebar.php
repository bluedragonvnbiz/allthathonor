<div class="box position-sticky top-0">
		<div class="logo p-3">
			<img src="<?= THEME_URL."/assets/images/logo-admin.svg" ?>" alt="All that Honors Club">
		</div>
		<div class="description">
			운영 및 문의
		</div>	
		<ul class="list-unstyled mb-0">
		<?php  
		$menu_arr = [
			["icon-admin-menu-1","웹사이트 관리","#"],
			["icon-admin-menu-2","1:1 문의","#"],
			["icon-admin-menu-3","약관 관리","#"],
		];

		foreach ($menu_arr as $key => $value) { 
		?>
			<li>
				<a href="#"<?php if($key == 0){echo ' class="active"';} ?>>
					<?= file_get_contents(THEME_URL."/assets/images/".$value[0].".svg").'<span class="text-truncate">'.$value[1].'</span>'; ?>
				</a>
			</li>
		<?php			
		}
		?>
		</ul>
		<div class="description">
			상품
		</div>	
		<ul class="list-unstyled mb-0">
		<?php  
		$menu_arr = [
			["icon-admin-menu-4","상품 관리","#"],
		];

		foreach ($menu_arr as $key => $value) { 
		?>
			<li>
				<a href="#">
					<?= file_get_contents(THEME_URL."/assets/images/".$value[0].".svg").'<span class="text-truncate">'.$value[1].'</span>'; ?>
				</a>
			</li>
		<?php			
		}
		?>
		</ul>
		<div class="description">
			멤버십
		</div>	
		<ul class="list-unstyled mb-0">
		<?php  
		$menu_arr = [
			["icon-admin-menu-5","멤버십 관리","#"],
			["icon-admin-menu-6","혜택/바우처 관리","#"]
		];

		foreach ($menu_arr as $key => $value) { 
		?>
			<li>
				<a href="#">
					<?= file_get_contents(THEME_URL."/assets/images/".$value[0].".svg").'<span class="text-truncate">'.$value[1].'</span>'; ?>
				</a>
			</li>
		<?php			
		}
		?>
		</ul>
</div>
