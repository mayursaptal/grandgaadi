jQuery(document).ready(function( $ ){
	$('.after-shipments-info #wpc-user-branch').change(function(){
		var selectedBranch = $('#wpc-user-branch option:selected').val();
		$.ajax({
            type:"POST",
            data:{
                action : 'display_branch_manager',
                selectedBranch : selectedBranch,
            },
            url : wpcBMFrontendAjaxHandler.ajaxurl,
            beforeSend:function(){
                //** Proccessing
                $('body').append('<div class="wpc-loading">Loading...</div>');
                $('.after-shipments-info #wpcargo_branch_manager').children('option:not(:first)').remove();
            },
            success:function(response){
				$('.after-shipments-info #wpcargo_branch_manager').html(response);
				$('.after-shipments-info #wpcargo_branch_manager').attr('disabled', false);
				$('.empty-branch-notice').hide();
				$('.wpc-loading').remove();
            }
        });
	});
});