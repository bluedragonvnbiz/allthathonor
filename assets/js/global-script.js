jQuery(document).ready(function($){
	$(".open-menu-mobile-btn").click(function(){
		$(".main-header .main-nav").slideToggle(200)
	})
	$(".input-file").on("change",function(){
        let el = $(this).closest(".group-element");
        let fileName = $(this).prop('files')[0].name;
        el.find(".file-name").val(fileName)
    })
    $(".phone-number").on("input", function(){
        let val = $(this).val().replace(/\D/g, ""); 

        if (val.length > 3 && val.length <= 7) {
            val = val.replace(/(\d{3})(\d{1,4})/, "$1-$2");
        } else if (val.length > 7) {
            val = val.replace(/(\d{3})(\d{4})(\d{1,4})/, "$1-$2-$3");
        }

        $(this).val(val);
    });
});//end ready

const elementPageLoad = '<div id="page_load"><span class="loader"> </span></div>';
function addLoading() {jQuery('body').append(elementPageLoad);}
function unLoading() {jQuery('#page_load').remove();}