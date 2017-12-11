( function( $, _ ) {
	var submit               = $( '#submit' ),
		notificationArea = $( document.getElementById( 'adstxt-notification-area' ) ),
		notificationTemplate = wp.template( 'adstext-notice' );

	submit.on( 'click', function( e ){
		e.preventDefault();

		var	textarea = $( '#adstxt_content' ),
			notices = $( '.adstxt-notice' ),
			ays = $( 'p.adstxt-ays'),
			submit_wrap = $( 'p.submit' ),
			spinner = submit_wrap.find( '.spinner' );

		spinner.addClass( 'is-active' );

		// clear any existing messages
		notices.remove();
		ays.remove();

		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: ajaxurl,
			data: $( '.adstxt-settings-form' ).serialize(),
			success: function(r) {
				spinner.removeClass( 'is-active' );

				if ( typeof r.sanitized != 'undefined' ) {
					textarea.val( r.sanitized )
				}

				if ( typeof r.saved != 'undefined' && r.saved ) {
					submit_wrap.before( '<div class="notice notice-success adstxt-notice"><p>' + adstxt.saved + '</p></div>' );
				} else if ( typeof r.errors != 'undefined' ) {
					submit.attr( 'disabled', 'disabled' );
					submit_wrap.before(
						'<div class="notice notice-error adstxt-errors adstxt-notice">' +
						'<p><strong>' + adstxt.error_intro + '</strong></p>' +
						'<ul class="adstxt-errors-items"></ul></div>' +
						'<p class="adstxt-ays"><input id="adstxt-ays-checkbox" name="adstxt_ays" type="checkbox" value="y" /> <label for="adstxt-ays-checkbox">' + adstxt.ays + '</label></p>'
					);

					for ( var i = 0; i < r.errors.length; i++ ) {
						$( '.adstxt-errors-items' ).append( '<li>' + r.errors[i] + '</li>' );
					}
				}
			}
		})
	});

	$( '.wrap' ).on( 'click', '#adstxt-ays-checkbox', function(e){
		submit.removeAttr( 'disabled' );
	})
})(jQuery);
