jQuery(document).ready(function($){
	$(".open-menu-mobile-btn").click(function(){
		$(".main-header .main-nav").slideToggle(200)
	})
});//end ready

const elementPageLoad = '<div id="page_load"><span class="loader"> </span></div>';
function addLoading() {jQuery('body').append(elementPageLoad);}
function unLoading() {jQuery('#page_load').remove();}