( function( $, _ ) {
	var submit               = $( document.getElementById( 'submit' ) ),
		notificationArea     = $( document.getElementById( 'adstxt-notification-area' ) ),
		notificationTemplate = wp.template( 'adstext-notice' ),
		editor               = wp.CodeMirror.fromTextArea( document.getElementById( 'adstxt_content' ), {
			lineNumbers: true
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
		notices.remove();

		// Copy the code mirror contents into for for submission.
		textarea.val( editor.getValue() );

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: $( '.adstxt-settings-form' ).serialize(),
			success: function( r ) {
				spinner.removeClass( 'is-active' );

				if ( 'undefined' !== typeof r.sanitized ) {
					textarea.val( r.sanitized );
				}

				if ( 'undefined' !== typeof r.saved && r.saved ) {
					data = {
						'class':   'success',
						'message': adstxt.saved
					};
					submit.removeAttr( 'disabled' );
				} else {
					data = {
						'class':   'error',
						'message': adstxt.error_intro,
						'errors': ( typeof r.errors != 'undefined' ) ? r.errors : [ adstxt.unknown_error ]
					}
				}
				notificationArea.html( notificationTemplate( data ) );
			}
		})
	});

	$( '.wrap' ).on( 'click', '#adstxt-ays-checkbox', function( e ) {
		if ( true === $( this ).prop('checked') ) {
			submit.removeAttr( 'disabled' );
		} else {
			submit.attr( 'disabled', 'disabled' );
		}
	} );

} )( jQuery, _ );
