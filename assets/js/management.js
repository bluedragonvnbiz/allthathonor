jQuery(document).ready(function($){
	var is_busy = false;
	$(".upload-box .select-file").on("change",function(){
        let el = $(this).closest(".upload-box");
        let fileName = $(this).prop('files')[0].name;
        el.find(".file-name").val(fileName)
    })
})// end ready