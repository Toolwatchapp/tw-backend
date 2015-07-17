jQuery( document ).ready( function ( $ ) {

	/**
	 * Show the file change settings when file change detection is enabled.
	 */
	$( "#itsec_file_change_enabled" ).change( function () {

		if ( $( "#itsec_file_change_enabled" ).is( ':checked' ) ) {

			$( "#file_change-settings" ).show();

		} else {

			$( "#file_change-settings" ).hide();

		}

	} ).change();

	/**
	 * Warns the user that they probably don't have enough RAM to perform a file scan
	 */
	if ( itsec_file_change.mem_limit <= 128 ) {

		$( "#itsec_file_change_enabled" ).change( function () {

			if ( this.checked ) {
				alert( itsec_file_change.text );

			}

		} );

	}

	/**
	 * Show the file tree in the settings.
	 */
	$( '.jquery_file_tree' ).fileTree(
		{
			root         : itsec_file_change.ABSPATH,
			script       : ajaxurl,
			expandSpeed  : - 1,
			collapseSpeed: - 1,
			multiFolder  : false

		}, function ( file ) {

			$( '#itsec_file_change_file_list' ).val( file.substring( itsec_file_change.ABSPATH.length ) + "\n" + $( '#itsec_file_change_file_list' ).val() );

		}, function ( directory ) {

			$( '#itsec_file_change_file_list' ).val( directory.substring( itsec_file_change.ABSPATH.length ) + "\n" + $( '#itsec_file_change_file_list' ).val() );

		}
	);

	/**
	 * Performs a one-time file scan
	 */
	$( '#itsec_one_time_file_check' ).submit( function ( event ) {

		event.preventDefault();

		var data = {
			action: 'itsec_file_change_ajax',
			nonce : itsec_file_change.nonce
		};

		//let user know we're working
		$( "#itsec_one_time_file_check_submit" ).removeClass( 'button-primary' ).addClass( 'button-secondary' ).attr( 'value', itsec_file_change.scanning_button_text );

		//call the ajax
		$.ajax(
			{
				url     : ajaxurl,
				type    : 'POST',
				data    : data,
				complete: function ( response ) {

					if ( response.responseText == 1 || response.responseText == - 1 ) {
						window.location.replace( 'admin.php?page=toplevel_page_itsec_logs&itsec_log_filter=file_change' )
					}

					$( "#itsec_one_time_file_check_submit" ).removeClass( 'button-secondary' ).addClass( 'button-primary' ).attr( 'value', itsec_file_change.button_text );

					if ( response.responseText == 0 ) {
						$( "#itsec_one_time_file_check_submit" ).hide();
						$( "#itsec_file_change_status" ).show().find( 'p' ).text( itsec_file_change.no_changes );
					}

				}
			}
		);

	} );

} );

jQuery( window ).load( function () {

	/**
	 * Shows and hides the red selector icon on the file tree allowing users to select an
	 * individual element.
	 */
	jQuery( document ).on( 'mouseover mouseout', '.jqueryFileTree > li a', function ( event ) {

		if ( event.type == 'mouseover' ) {

			jQuery( this ).children( '.itsec_treeselect_control' ).css( 'visibility', 'visible' );

		} else {

			jQuery( this ).children( '.itsec_treeselect_control' ).css( 'visibility', 'hidden' );

		}

	} );

} );
