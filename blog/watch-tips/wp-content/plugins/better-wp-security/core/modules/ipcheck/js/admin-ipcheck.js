jQuery( document ).ready( function () {

//process tooltip actions
	jQuery( '.itsec_reset_ipcheck_api_key' ).click( function ( event ) {

		event.preventDefault();

		var button = this;

		var data = {
			action : 'itsec_api_key_ajax',
			nonce  : itsec_ipcheck.api_nonce
		};

		//call the ajax
		jQuery.ajax(
			{
				url      : ajaxurl,
				type     : 'POST',
				data     : data,
				complete : function ( response ) {

					var new_content = '<input id="itsec_ipcheck_email"  class="regular-text" name="itsec_ipcheck[email-brute-force]" value="" type="text" placeholder="' + itsec_ipcheck.text3 + '"><br /><input id="itsec_ipcheck_optin" name="itsec_ipcheck[optin-brute-force]" value="1" checked="" type="checkbox"><label for="itsec_ipcheck_optin">' + itsec_ipcheck.text1 + '</label><p class="description">' + itsec_ipcheck.text2 + '</p>';
					var new_enable = '';

					jQuery( '.itsec_api_key_field' ).replaceWith( new_content );
					jQuery( '#itsec_ipcheck_api_ban' ).parent().parent().replaceWith( new_enable );

				}
			}
		);

	} );

} );