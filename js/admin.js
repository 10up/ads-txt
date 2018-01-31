( function( $, _ ) {
	var submit               = $( document.getElementById( 'submit' ) ),
		notificationArea     = $( document.getElementById( 'adstxt-notification-area' ) ),
		notificationTemplate = wp.template( 'adstext-notice' ),
		editor               = wp.CodeMirror.fromTextArea( document.getElementById( 'adstxt_content' ), {
			lineNumbers: true,
			mode: 'shell'
		} );

	submit.on( 'click', function( e ){
		e.preventDefault();

		var	textarea    = $( document.getElementById( 'adstxt_content' ) ),
			notices     = $( '.adstxt-notice' ),
			submit_wrap = $( 'p.submit' ),
			spinner     = submit_wrap.find( '.spinner' );

		submit.attr( 'disabled', 'disabled' );
		spinner.addClass( 'is-active' );

		// clear any existing messages
		notificationArea.hide();
		notices.remove();

		// Copy the code mirror contents into form for submission.
		textarea.val( editor.getValue() );

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: $( '.adstxt-settings-form' ).serialize(),
			success: function( r ) {
				var templateData = {};

				spinner.removeClass( 'is-active' );

				if ( 'undefined' !== typeof r.sanitized ) {
					textarea.val( r.sanitized );
				}

				if ( 'undefined' !== typeof r.saved && r.saved ) {
					templateData.saved = {
						'saved_message': adstxt.saved_message
					};
				} else {
					templateData.errors = {
						'error_message': adstxt.unknown_error
					}
				}

				if ( 'undefined' !== typeof r.errors && r.errors.length > 0 ) {
					templateData.errors = {
						'error_message': adstxt.error_message,
						'errors':        r.errors
					}
				}
				notificationArea.html( notificationTemplate( templateData ) ).show();
			}
		})
	});

	$( '.wrap' ).on( 'click', '#adstxt-ays-checkbox', function( e ) {
		if ( true === $( this ).prop( 'checked' ) ) {
			submit.removeAttr( 'disabled' );
		} else {
			submit.attr( 'disabled', 'disabled' );
		}
	} );

	editor.on( 'change', function() {
		$( '.adstxt-ays' ).remove();
		submit.removeAttr( 'disabled' );
	} );

} )( jQuery, _ );
