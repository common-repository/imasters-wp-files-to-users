// JavaScript Document

( function($) {

	$( function($) {
				
		FILE.delete_file();
                FILE.iwpftu_uninstall_button();
	
	});

	FILE = {
	
		delete_file : function() {
			
			$('#delete-file').click( function() {
				var confirm_delete = confirm( confirm_delete_message );
				
				if ( !confirm_delete )
					return false;
			});
			
		},

                iwpftu_uninstall_button : function() {
                    var button = $('input[name="do"]');
                    var checkbox = $('#uninstall_iwpftu_yes');
                    button.hide();
                    checkbox.attr( 'checked', '' ).click(function() {
                        var is_checked = checkbox.attr( 'checked' );
                        if ( is_checked )
                            button.fadeIn();
                        else
                            button.fadeOut();
                    })
                }
		
	}
	
})(jQuery);