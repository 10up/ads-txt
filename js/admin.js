( function( $, _ ) {
	var submit               = $( document.getElementById( 'submit' ) ),
		notificationArea     = $( document.getElementById( 'adstxt-notification-area' ) ),
		notificationTemplate = wp.template( 'adstext-notice' ),
		editor               = wp.CodeMirror.fromTextArea( document.getElementById( 'adstxt_content' ), {
			lineNumbers: true,
			mode: 'shell'
		} );

	function checkForAdsFile( e ) {
		var spinner = $( '.existing-adstxt .spinner' );

		if ( false !== e ) {
			spinner.addClass( 'is-active' );
			e.preventDefault();
		}

		var adstxt_type = $('input[name=adstxt_type]').val();
		var wpnonce = $('input[name=_wpnonce]').val();

		$.get({
			url: window.ajaxurl,
			type: 'POST',
			data: {
				action: 'adstxts_check_for_existing_file',
				adstxt_type: (adstxt_type === "" || adstxt_type === undefined) ? null : adstxt_type,
				_wpnonce: wpnonce,
			},
			success: function(response) {
				spinner.removeClass( 'is-active' );
				if ( ! response.file_exist ) {
					// Ads.txt not found
					$( '.existing-adstxt' ).hide();
				} else {
					$( '.existing-adstxt' ).show();
				}
			}
		});
	}

	// Call our check when we first load the page
	checkForAdsFile( false );

	$( '.ads-txt-rerun-check' ).on( 'click', checkForAdsFile );

	submit.on( 'click', function( e ){
		e.preventDefault();

		var	textarea    = $( document.getElementById( 'adstxt_content' ) ),
			notices     = $( '.adstxt-notice' ),
			submit_wrap = $( 'p.submit' ),
			saveSuccess = false,
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
					saveSuccess = true;
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

				// Refresh after a successful save, otherwise show the error message.
				if ( saveSuccess ) {
					document.location = document.location + '&ads_txt_saved=1';
				} else {
					notificationArea.html( notificationTemplate( templateData ) ).show();
				}

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
