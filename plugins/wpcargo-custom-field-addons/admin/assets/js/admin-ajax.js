jQuery(document).ready(function($) {
    $('.delete-field').click(function(e){
		e.preventDefault();
		if(confirm("Are you sure you want to delete this field?"))  {
		var parentContainer = $(this).parent().attr('id');
		var parentList = $(this).parent().parent().parent().attr('id');	
		$.ajax({
			type:"POST",
			data:{
				action:'delete_CF',	
				cfID:$(this).attr('data-id'),
			},
			url : deleteCFhandler.ajax_url,
			beforeSend:function(){
				$('body').append("<div class='wpcargo-loading'>Loading...</div>");
			},
			success:function(data){
				$('#'+ parentList ).remove();
				$('body .wpcargo-loading' ).remove();
			}
		});
		}
	});
   $('#field-type').on('change', function(){
		   var selectedValue = $(this).val();
		   if( selectedValue == 'select' || selectedValue == 'multiselect' || selectedValue == 'radio' || selectedValue == 'checkbox' ){
			   $("#select-list td textarea").removeAttr('readonly');
			   $("#select-list td textarea").prop('required',true);
		   }else{
			   $("#select-list td textarea").attr('readonly','readonly');
			   $("#select-list td textarea").prop('required',false);
		   }
   });
   $('#field-select input').on('change', function(){
       var selectedValue = $(this).val();
		if( selectedValue == 'new' ){
			$('#new').css('display','block');
			$('#new input').attr('name', 'field_key');
			$('#new input').attr('required', 'required');
			
			$('#existing').css('display','none');
			$("#existing option[value='']").attr('selected', true);
			$("#existing select").attr('name', 'dummy');
			$("#existing select").removeAttr('required');
		}else{
			$('#new').css('display','none');
			$('#new input').attr('name', 'dummy');
			$("#new input").removeAttr('required');
			
			$('#existing').css('display','block');
			$("#existing select").attr('name', 'field_key');
			$("#existing select").attr('required', 'required');
		}
   });
   
});