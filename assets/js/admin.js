jQuery(document).ready(function($){
	var is_busy = false;
	$('.remove-item-btn').click(function(){
		if (!confirm('삭제하시겠습니까?')) {
		    return;
		}
		if(is_busy) return; is_busy = true;
		$.ajax({
         url: ajax_url,
         type: "post",
         dataType: "text",
         data: {                  
             action: 'admin_remove_contact_item', 
             token:$("#pv-token").val(),
             id:$(this).data("id"), 
             type:$("#pv-type").val()                                                                                                                                                
         },

         success: function(output) {            
           	location.reload();
            is_busy = false;
         }          

     });
	})
})