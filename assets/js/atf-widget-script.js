jQuery( document ).ready( function(){

	// Widget Saved
	jQuery( document ).ajaxSuccess( function( e, xhr, settings ) {
		// re-initiate the colour picker
		if( settings.data.search( 'action=save-widget' ) != -1 ) {

		trigger_color_picker();

		}
	}); // END AJAX success

	trigger_color_picker();
	
	function trigger_color_picker() {
		var colorInput = jQuery('input.color-field')
		//Initialize color picker for buttons
		colorInput.wpColorPicker();
	}

});