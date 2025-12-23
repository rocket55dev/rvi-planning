jQuery(window).on('load', function () {
	let textarea = document.getElementById("g-recaptcha-response-100000");
	if ( textarea ) {
		textarea.setAttribute("aria-hidden", "true");
		textarea.setAttribute("aria-label", "do not use");
		textarea.setAttribute("aria-readonly", "true");
	}

	let iframe = jQuery('.grecaptcha-logo iframe');
	if ( iframe ) {
		iframe.attr('title', 'Protected by reCaptcha');
	}
});
