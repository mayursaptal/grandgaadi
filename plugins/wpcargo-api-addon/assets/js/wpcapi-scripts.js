jQuery(document).ready(function($){
	$( '#reset-wpcapi' ).click(function(e){
		e.preventDefault();
		$.ajax({
			type:"POST",
			data:{
				action	: 'reset_wpcapi',
			},
			url : wpcaAPIAjaxHandler.ajaxurl,
			beforeSend:function(){
				$('body').append('<div class="wpc-loading">Loading...</div>');
			},
			success:function(data){
				console.log(data);
				$('#wpcapi-user-api').text(data);
				$('body .wpc-loading').remove();
				return false;
			}
		});
	});
});