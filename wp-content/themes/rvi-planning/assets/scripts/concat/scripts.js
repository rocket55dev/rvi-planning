/**
 * Global JS
 *
 */

(function($){

	$(window).on( 'load', function(){

		/**
		 * External Link Targeting
		 */
		const site_hostname = window.location.hostname;
		$( 'a:not([href*="' + site_hostname + '"]):not([href^="#"]):not([href^="/"])' ).attr( 'target', '_blank' );
		$( 'a[href$=".pdf"]' ).attr( 'target', '_blank' );

		/**
		 * CF7 a11y Fixes
		 */
		var wpcf7Elm = document.querySelector( '.wpcf7' ),
			screenReaderResponse = $( '.screen-reader-response' );
		if ( document.querySelector( '.wpcf7' ) ) {
			screenReaderResponse.attr( 'aria-hidden', 'true' );
			wpcf7Elm.addEventListener( 'wpcf7invalid', function( event ) {
				var error = $( '.wpcf7-not-valid-tip' ),
					message = $( '.wpcf7-response-output' ),
					badInput = $( 'input[aria-invalid="true"]');
				message.attr( 'role', 'alert' );
				error.removeAttr( 'aria-hidden' );
				badInput.removeAttr( 'aria-describedby' );
				setTimeout(function(){
					document.querySelector('input[aria-invalid="true"]').focus();
				}, 100);
			}, false );
		}

		function cf7ResponseMessage() {
			const response = $('.wpcf7-response-output');
			if ( document.querySelector( '.wpcf7' ) ) {
				response.attr('role', 'status');
				response.attr('aria-hidden', 'false');
			}
		}
		document.addEventListener( 'wpcf7invalid', cf7ResponseMessage, false );
		document.addEventListener( 'wpcf7spam', cf7ResponseMessage, false );
		document.addEventListener( 'wpcf7mailfailed', cf7ResponseMessage, false );
		document.addEventListener( 'wpcf7mailsent', cf7ResponseMessage, false );

	});

})(jQuery);
