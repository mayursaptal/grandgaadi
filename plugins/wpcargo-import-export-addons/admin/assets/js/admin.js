// JavaScript Document
jQuery(document).ready(function($) {
	/* Import / Export page script*/
    $('#meta-fields').multiselect2side({
		selectedPosition: 'right',
		moveOptions: false,
		labelsx: '',
		labeldx: '',
		//autoSort: false,
		//autoSortAvailable: true
	});
	$(".wpcie-datepicker").datetimepicker({
		timepicker:false,
		format: 'Y-m-d'		
	});
});