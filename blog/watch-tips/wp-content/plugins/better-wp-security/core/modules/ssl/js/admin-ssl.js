jQuery( document ).ready( function () {

	jQuery( '#itsec_ssl_admin' ).change( function () {

		if ( this.checked ) {

			var ssl_confirm = confirm( ssl_warning_text.text );

			if ( ssl_confirm == false ) {

				jQuery( '#itsec_ssl_admin' ).attr( 'checked', false );

			}

		}

	} );

} );
