jQuery(document).ready(function($){
	$( '#generate-api-key' ).click(function(e){
		e.preventDefault();
		$.ajax({
			type:"POST",
			data:{
				action	: 'generate_wpcapi',
			},
			url : wpcaAPIAjaxHandler.ajaxurl,
			beforeSend:function(){
				$('body').append('<div class="wpc-loading">Loading...</div>');
			},
			success:function(data){
				$('#wpcargo_api').val(data);
				$('body .wpc-loading').remove();
				return false;
			}
		});
	});
});