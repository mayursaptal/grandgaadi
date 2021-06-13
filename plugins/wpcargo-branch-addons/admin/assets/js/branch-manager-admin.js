jQuery(document).ready(function( $ ){
    'use strict';
    
    $('#wpcbranch-restriction').on('click', '.wpcbranch_access', function(){
        var optValue    = $(this).prop("checked") === true ? 1 : 0 ;
        var optName     = $(this).attr('name');
        $.ajax({
            type:"POST",
            data:{
                action:'wpcbranch_access',
                optValue: optValue,
                optName: optName,
            },
            url : wpcBMAjaxHandler.ajaxurl,
            beforeSend:function(){
                $('body').append('<div class="wpc-loading">Loading...</div>');
            },
            success:function( response ){
                $('body .wpc-loading').remove();
            }
        });
    });

	$('#add-branch').on('click', function( e ){
		e.preventDefault();
		$('#addBranchModal').css({'display':'block'});
		$('.select-bm').select2({
			placeholder: '',
			allowClear: true
		});
	});
    $('#wpc-branch-wrapper').on('click', '.edit', function( e ){
        e.preventDefault();
        var branchID = $(this).attr('data-id');
        $.ajax({
            type:"POST",
            dataType: "json",
            data:{
                action:'get_branch',
                branchID: branchID,
            },
            url : wpcBMAjaxHandler.ajaxurl,
            beforeSend:function(){
                //** Proccessing
                $('body').append('<div class="wpc-loading">Loading...</div>');
            },
            success:function( response ){
                $('#edit-branch #update-branch-name').val(response.name);
                $('#edit-branch #update-branch-code').val(response.code);
                $('#edit-branch #update-branch-phone').val(response.phone);
                $('#edit-branch #update-branch-address1').val(response.address1);
                $('#edit-branch #update-branch-address2').val(response.address2);
                $('#edit-branch #update-branch-city').val(response.city);
                $('#edit-branch #update-branch-postcode').val(response.postcode);
                $('#edit-branch #update-branch-country').val(response.country);
                $('#edit-branch #update-branch-state').val(response.state);
				var branchManager   = [];
				var branchClient    = [];
				var branchAgent     = [];
				var branchEmployee  = [];
				var branchDriver    = [];
				if(response.branch_manager != null){
				   branchManager = response.branch_manager.split(',');
                }
                if(response.branch_client != null){
                    branchClient = response.branch_client.split(',');
                 }
                 if(response.branch_agent != null){
                    branchAgent = response.branch_agent.split(',');
                 }
                 if(response.branch_employee != null){
                    branchEmployee = response.branch_employee.split(',');
                 }
                 if(response.branch_driver != null){
                    branchDriver = response.branch_driver.split(',');
                 }
				$.each( branchManager, function(key, value){
					$('#edit-branch #update-branch-manager option[value=' + value + ']').attr('selected', true);
                });
                $.each( branchClient, function(key, value){
					$('#edit-branch #update-branch-client option[value=' + value + ']').attr('selected', true);
                });
                $.each( branchAgent, function(key, value){
					$('#edit-branch #update-branch-agent option[value=' + value + ']').attr('selected', true);
                });
                $.each( branchEmployee, function(key, value){
					$('#edit-branch #update-branch-employee option[value=' + value + ']').attr('selected', true);
                });
                $.each( branchDriver, function(key, value){
					$('#edit-branch #update-branch-driver option[value=' + value + ']').attr('selected', true);
				});
                $('#edit-branch #branchid').val(response.id);
                $('#editBranchModal').css({'display':'block'});
                $('body .wpc-loading').remove();
				$('.select-bm').select2({
					placeholder: '',
					allowClear: true
				});
            }
        });
    });
	$('.modal .close').on('click', function(e){
		e.preventDefault();
		$('.modal').css({'display':'none'});
	});

	$('form#add-branch').submit(function( e ){
		e.preventDefault();
		var formID			= '#'+ $(this).attr('id');
		var branchName		= $( formID+ ' #branch-name').val();
		var branchCode		= $( formID+ ' #branch-code').val();
		var branchPhone		= $( formID+ ' #branch-phone').val();
		var branchAddress1	= $( formID+ ' #branch-address1').val();
        var branchAddress2	= $( formID+ ' #branch-address2').val();
        var branchCity		= $( formID+ ' #branch-city').val();
        var branchPostcode	= $( formID+ ' #branch-postcode').val();
        var branchCountry	= $( formID+ ' #branch-country').val();
        var branchState		= $( formID+ ' #branch-state').val();
        var branchManager	= $( formID+ ' #branch-manager').val();
        var branchEmployee	= $( formID+ ' #branch-employee').val();
        var branchAgent	    = $( formID+ ' #branch-agent').val();
        var branchClient	= $( formID+ ' #branch-client').val();
        var branchDriver	= $( formID+ ' #branch-driver').val();
		
		//** Process Data
		$.ajax({
            type:"POST",
            data:{
                action			: 'add_branch',
                branchName		: branchName,
                branchCode		: branchCode,
                branchPhone		: branchPhone,
                branchAddress1	: branchAddress1,
                branchAddress2	: branchAddress2,
                branchCity		: branchCity,
                branchPostcode	: branchPostcode,
                branchCountry	: branchCountry,
                branchState		: branchState,
                branchManager	: branchManager,
                branchEmployee  : branchEmployee,
                branchAgent     : branchAgent,
                branchClient    : branchClient,
                branchDriver    : branchDriver
            },
            url : wpcBMAjaxHandler.ajaxurl,
            beforeSend:function(){
				$('body').append('<div class="wpc-loading">Loading...</div>');
            },
            success:function(response){
            	if(response){
            		location.reload();
            	}else{
            		alert(wpcBMAjaxHandler.errormessage);
            	}
            	$('.modal .close').trigger('click');
            	$('body .wpc-loading').remove();
            }
        });
	});
    $('form#edit-branch').submit(function( e ){
        e.preventDefault();
        var formID			= '#'+ $(this).attr('id');
        var branchName		= $( formID+ ' #update-branch-name').val();
        var branchCode		= $( formID+ ' #update-branch-code').val();
        var branchPhone		= $( formID+ ' #update-branch-phone').val();
        var branchAddress1	= $( formID+ ' #update-branch-address1').val();
        var branchAddress2	= $( formID+ ' #update-branch-address2').val();
        var branchCity		= $( formID+ ' #update-branch-city').val();
        var branchPostcode	= $( formID+ ' #update-branch-postcode').val();
        var branchCountry	= $( formID+ ' #update-branch-country').val();
        var branchState		= $( formID+ ' #update-branch-state').val();
        var branchManager	= $( formID+ ' #update-branch-manager').val();
        var branchEmployee	= $( formID+ ' #update-branch-employee').val();
        var branchAgent	    = $( formID+ ' #update-branch-agent').val();
        var branchClient	= $( formID+ ' #update-branch-client').val();
        var branchDriver	= $( formID+ ' #update-branch-driver').val();
        var branchid		= $( formID+ ' #branchid').val();
        //** Process Data
        $.ajax({
            type:"POST",
            data:{
                action			: 'update_branch',
                branchName		: branchName,
                branchCode		: branchCode,
                branchPhone		: branchPhone,
                branchAddress1	: branchAddress1,
                branchAddress2	: branchAddress2,
                branchCity		: branchCity,
                branchPostcode	: branchPostcode,
                branchCountry	: branchCountry,
                branchState		: branchState,
                branchManager	: branchManager,
                branchEmployee  : branchEmployee,
                branchAgent     : branchAgent,
                branchClient    : branchClient,
                branchDriver    : branchDriver,
                branchid		: branchid
            },
            url : wpcBMAjaxHandler.ajaxurl,
            beforeSend:function(){
                //** Proccessing
                $('body').append('<div class="wpc-loading">Loading...</div>');
            },
            success:function( response ){
                if( response ){
                    location.reload();               
                }else{
                    alert(wpcBMAjaxHandler.errormessage);
                }
                $('.modal .close').trigger('click');
                $('body .wpc-loading').remove();
            }
        });
    });
	$('#wpc-branch-wrapper').on( 'click', '.delete', function( e ){
		e.preventDefault();
		var branchID = $(this).attr('data-id');
        if(confirm( wpcBMAjaxHandler.deleteConfirmation)){
            //** Process Data
            $.ajax({
                type:"POST",
                data:{
                    action		: 'delete_branch',
                    branchID	: branchID,
                },
                url : wpcBMAjaxHandler.ajaxurl,
                beforeSend:function(){
                    //** Proccessing
                    $('body').append('<div class="wpc-loading">Loading...</div>');
                },
                success:function( response ){
                    if( response ){
                        $('#wpc-branch-wrapper #branch-'+branchID).remove();
                    }else{
                        alert(wpcBMAjaxHandler.errormessage);
                    }
                    $('body .wpc-loading').remove();
                }
            });
        }
	});
    $('input#shipment-number').bind('paste', function(e){       
        setTimeout(function() { 
            var branch          = $('#shipment-branch').val();
            var shipmentNumber  = $('#shipment-number').val();
            if( !branch ){
                $('#shipment-branch').focus();
                return false;
            }   
            console.log(shipmentNumber);     
            transfer_shipment_branch(branch, shipmentNumber);
        }, 100);
        
    });
    $('#transfer-shipment-branch').keypress(function( e ){
        if(e.which == 13) {     
            var branch          = $('#shipment-branch').val();
            var shipmentNumber  = $('#shipment-number').val();
            if( !branch ){
                $('#shipment-branch').focus();
                return false;
            }
            console.log(shipmentNumber);
            transfer_shipment_branch( branch, shipmentNumber );
        }
    });
    $('#transfer-shipment-branch #shipment-branch').on('change', function(){
        $('#shipment-number').focus();
    });
    function transfer_shipment_branch( branch, shipmentNumber ){
        //** Process Data
        $.ajax({
            type:"POST",
            data:{
                action:'transfer_branch',
                branch: branch,
                shipmentNumber:shipmentNumber
            },
            url : wpcBMAjaxHandler.ajaxurl,
            beforeSend:function(){
                //** Proccessing
                $('body').append('<div class="wpc-loading">Loading...</div>');
            },
            success:function( response ){
                if(response){
                    $('body').prepend( '<div class="transfer-message success"><p>'+wpcBMAjaxHandler.transferSuccess+'</p></div>' );
                }else{
                    $('body').prepend( '<div class="transfer-message error"><p>'+wpcBMAjaxHandler.transferError+'</p></div>' );
                }
                $('#transfer-shipment-branch input#shipment-number').val('');
                setTimeout(function(){ $('body .transfer-message').remove(); }, 2000);
                $('body .wpc-loading').remove();
            }
        });
    }
});