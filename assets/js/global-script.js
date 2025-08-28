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
        if (val.length > 12) {
            val = val.substring(0, 12);
        }
        if (val.length > 3 && val.length <= 7) {
            val = val.replace(/(\d{3})(\d{1,4})/, "$1-$2");
        } else if (val.length > 7) {
            val = val.replace(/(\d{3})(\d{4})(\d{1,4})/, "$1-$2-$3");
        }

        $(this).val(val);
    });
    $(".biznum").on("input", function(){
        let val = $(this).val().replace(/\D/g, ""); // chỉ lấy số

        // Giới hạn đúng 10 số
        if (val.length > 10) {
            val = val.substring(0, 10);
        }

        // Định dạng ###-##-#####
        if (val.length > 3 && val.length <= 5) {
            val = val.replace(/(\d{3})(\d{1,2})/, "$1-$2");
        } else if (val.length > 5) {
            val = val.replace(/(\d{3})(\d{2})(\d{1,5})/, "$1-$2-$3");
        }

        $(this).val(val);
    });

    $(".custom-number-input").on("click", " .up-arrow", function () {
        let $input = $(this).closest(".custom-number-input").find("input[type=number]");
        let currentVal = parseInt($input.val()) || 0;
        let max = $input.attr("max") ? parseInt($input.attr("max")) : Infinity;

        if (currentVal < max) {
            $input.val(currentVal + 1).trigger("change");
        }
    });

    $(".custom-number-input").on("click", ".down-arrow", function () {
        let $input = $(this).closest(".custom-number-input").find("input[type=number]");
        let currentVal = parseInt($input.val()) || 0;
        let min = $input.attr("min") ? parseInt($input.attr("min")) : -Infinity;

        if (currentVal > min) {
            $input.val(currentVal - 1).trigger("change");
        }
    });
});//end ready

const elementPageLoad = '<div id="page_load"><span class="loader"> </span></div>';
function addLoading() {jQuery('body').append(elementPageLoad);}
function unLoading() {jQuery('#page_load').remove();}