jQuery(document).ready(function($) {
	var podCustomMeta = wpcargoPODAJAXHandler.custom_meta;

	/*$( "#sortable" ).sortable({
		revert: true
	});*/

	$('#shipment-list').on('click', '.show-signaturepad', function(){
		var shipmentID = $(this).attr('data-id');
		$.ajax({
            type:"POST",
            data:{
                action  : 'show_signaturepad',    
                sid   : shipmentID
            },
            url : wpcargoPODAJAXHandler.ajaxurl,
            beforeSend:function(){
                $('body').append('<div class="wpcargo-loading">Loading...</div>');
            },
            success:function( response ){
                $('body .wpcargo-loading').remove();
	            $('#pod-modal .modal-body').html(response);
				submit_modal();
            }
        });
    })
    // POD Sign Submit
	function submit_modal(){
		$('#pod-sign-submit').on('click', function(){
			var shipmentID = $('#pod-modal #shipment-id').val();
			var location   = $('#wpcargo-pod-location').val();
			var status     = $('#wpcargo-pod-status').val();
			var signature  = $('#wpcargo-pod-signature').val();
			var notes      = $('#wpcargo-pod-notes').val();
			var custommeta = [];
			if( podCustomMeta.length > 0 ){
				$.each( podCustomMeta, function( index, value ){
					if( value.type == 'radio' ){
						var postValue = $('.'+value.meta_key+':checked').val();
					}else{
						var postValue = $('.'+value.meta_key).val();
					}
					var postKey = value.meta_key;
					custommeta.push( {postKey:postKey, postValue:postValue} );
				});
			}
			$.ajax({
				type: "POST",
				url: wpcargoPODAJAXHandler.ajaxurl,
				data:{
					action: 'pod_signed',
					shipmentID: shipmentID,
					location : location,
					notes: notes,
					status : status,
					signature: signature,
					custommeta: custommeta,
				},
				beforeSend:function(){
					$('body').append('<div class="wpcargo-loading">Loading...</div>');
				},
				success:function(response){
					$('body .wpcargo-loading').remove();
					window.location.reload();
				}
			});
		});
	}
    //prevent disabling scroll in modal popup
    if( $('#pod-modal').length ){
        $('body').click( function(){
            $(this).find('.media-modal').click( function(){
                setTimeout( function(){ $('.wpcargo-dashboard').addClass('modal-open');}, 300);
            });
        });
    }    
	// Export report Script
	$('#wpcpod-export').on('submit', function( e ){
		e.preventDefault();
		var driverID 	= $('#wpcpod-export #assign_driver').val();
		var status 		= $('#wpcpod-export #shipment_status').val();
		var dateFrom 	= $('#wpcpod-export #date_from').val();
		var dateTo 		= $('#wpcpod-export #date_to').val();
		$.ajax({
			type: "POST",
			url: wpcargoPODAJAXHandler.ajaxurl,
			data:{
				action: 'wpcpod_generate_report',
				driverID: driverID,
				status : status,
				dateFrom: dateFrom,
				dateTo : dateTo
			},
			beforeSend:function(){
				$('body').append('<div class="wpcargo-loading">Loading...</div>');
				$('#wpcpod-export-progress').html('');
			},
			success:function(response){
				if( response.rows == 0 ){
					$('#wpcpod-export-progress').prepend('<div class="alert alert-danger text-center">'+response.message+'</div>');
				}else{
					$('#wpcpod-export-progress').prepend('<div class="alert alert-success text-center">'+response.message+'</div>');
					download_file( response.file_url, response.file_name);
					setTimeout(function(){
						$('#wpcpod-export-progress').html('');
					}, 3000 );
				}
				$('body .wpcargo-loading').remove();
			}
		});
	});
	function download_file(fileURL, fileName) {
        // for non-IE
        if (!window.ActiveXObject) {
            var save = document.createElement('a');
            save.href = fileURL;
            save.target = '_blank';
            var filename = fileURL.substring(fileURL.lastIndexOf('/')+1);
            save.download = fileName || filename;
            if ( navigator.userAgent.toLowerCase().match(/(ipad|iphone|safari)/) && navigator.userAgent.search("Chrome") < 0) {
                    document.location = save.href; 
                // window event not working here
                }else{
                    var evt = new MouseEvent('click', {
                        'view': window,
                        'bubbles': true,
                        'cancelable': false
                    });
                    save.dispatchEvent(evt);
                    (window.URL || window.webkitURL).revokeObjectURL(save.href);
                }	
        }
        // for IE < 11
        else if ( !! window.ActiveXObject && document.execCommand)     {
            var _window = window.open(fileURL, '_blank');
            _window.document.close();
            _window.document.execCommand('SaveAs', true, fileName || fileURL)
            _window.close();
        }
    }
});