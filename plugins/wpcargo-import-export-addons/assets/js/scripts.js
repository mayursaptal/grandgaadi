jQuery(document).ready(function($) {
	const ajaxURL = wpcieAjaxHandler.ajax_url;

	const loadingUI = function(){
		$( '#wpcie-loader-wrapper').addClass('display-none');
		$( '#wpcie-fields-wrapper').removeClass('display-none');
	}

	const downloadFile =  function(fileURL, fileName) {
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
	// Preparing files
	loadingUI();

	if ($.isFunction( $.fn.pickadate )) {
		// Get the elements
		var from_input = $('#wpcie-export-form #startingDate').pickadate({
			format: 'yyyy-mm-dd',
		}),
		from_picker = from_input.pickadate('picker');
		var to_input = $('#wpcie-export-form #endingDate').pickadate({
			format: 'yyyy-mm-dd',
		}),
		to_picker = to_input.pickadate('picker');
		
		if( from_picker && to_picker ){

			// Check if there’s a “from” or “to” date to start with and if so, set their appropriate properties.
			if (from_picker.get('value')) {
				to_picker.set('min', from_picker.get('select'))
			}
			if (to_picker.get('value')) {
				from_picker.set('max', to_picker.get('select'))
			}
			
			// Apply event listeners in case of setting new “from” / “to” limits to have them update on the other end. If ‘clear’ button is pressed, reset the value.
			from_picker.on('set', function (event) {
				if (event.select) {
					to_picker.set('min', from_picker.get('select'))
				} else if ('clear' in event) {
					to_picker.set('min', false)
				}
			});
			to_picker.on('set', function (event) {
				if (event.select) {
					from_picker.set('max', to_picker.get('select'))
				} else if ('clear' in event) {
					from_picker.set('max', false)
				}
			});
		}
	}
	var input = $('#shipper_name').val();
	var options = function() {
		var returned_info = null;
		$.ajax({
			method: 'POST',
			url: ajaxURL,
			data: {
				action : 'search_shipper',
				input : input,
			},
			dataType: 'json',
			async: false,
			global: false,
			success: function(data) {
				returned_info = data;
			}
		});
		return returned_info;
	}();
	
	if( $('input#shipper_name').length > 0 ){
		function createString(arr, key) {
			return arr.map(function (obj) {
				return obj[key];
			}).join(', ');
		}
		var array_string = createString(options, 'label').split(",");
			$('input#shipper_name').mdbAutocomplete({
			data: array_string,
		});
	}
});