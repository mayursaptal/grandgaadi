jQuery(document).ready(function($){
    // Check if Frontend Manager Date Picker is disable
    if( !wpcfeDateTimeAjaxhandler.disableDatepicker ){
        $('.wpccf-datepicker').pickadate({
            format: wpcfeDateTimeAjaxhandler.dateFormat,
        });
    }
    // Check if Frontend Manager Time Picker is disable
    if( !wpcfeDateTimeAjaxhandler.disableTimepicker ){
        $('.wpccf-timepicker').pickatime({
            twelvehour: wpcfeDateTimeAjaxhandler.timeFormat,
        });
    }
});