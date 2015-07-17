<?php
/**
 * WordPress Permissions check code from Serverbuddy by PluginBuddy written by Dustin Bolton of iThemes
 */
global $itsec_globals;

$tests = array();

//BEGIN FOLDERS
$this_test = array(
	'title'      => '/',
	'suggestion' => '= 755',
	'value'      => substr( sprintf( '%o', fileperms( ABSPATH . '/' ) ), - 4 ),
);

if ( ! fileperms( ABSPATH . '/' ) || 755 != substr( sprintf( '%o', fileperms( ABSPATH . '/' ) ), - 4 ) ) {

	$this_test['status'] = 'WARNING';

} else {

	$this_test['status'] = 'OK';

}

array_push( $tests, $this_test );

$this_test = array(
	'title'      => '/wp-includes/',
	'suggestion' => '= 755',
	'value'      => substr( sprintf( '%o', fileperms( ABSPATH . '/wp-includes/' ) ), - 4 ),
);

if ( ! fileperms( ABSPATH . '/wp-includes/' ) || 755 != substr( sprintf( '%o', fileperms( ABSPATH . '/wp-includes/' ) ), - 4 ) ) {

	$this_test['status'] = 'WARNING';

} else {

	$this_test['status'] = 'OK';

}

array_push( $tests, $this_test );


$this_test = array(
	'title'      => '/wp-admin/',
	'suggestion' => '= 755',
	'value'      => substr( sprintf( '%o', fileperms( ABSPATH . '/wp-admin/' ) ), - 4 ),
);

if ( ! fileperms( ABSPATH . '/wp-admin/' ) || 755 != substr( sprintf( '%o', fileperms( ABSPATH . '/wp-admin/' ) ), - 4 ) ) {

	$this_test['status'] = 'WARNING';

} else {

	$this_test['status'] = 'OK';

}

array_push( $tests, $this_test );


$this_test = array(
	'title'      => '/wp-admin/js/',
	'suggestion' => '= 755',
	'value'      => substr( sprintf( '%o', fileperms( ABSPATH . '/wp-admin/js/' ) ), - 4 ),
);

if ( ! fileperms( ABSPATH . '/wp-admin/js/' ) || 755 != substr( sprintf( '%o', fileperms( ABSPATH . '/wp-admin/js/' ) ), - 4 ) ) {

	$this_test['status'] = 'WARNING';

} else {

	$this_test['status'] = 'OK';

}

array_push( $tests, $this_test );


$this_test = array(
	'title'      => get_theme_root(),
	'suggestion' => '= 755',
	'value'      => substr( sprintf( '%o', fileperms( get_theme_root() ) ), - 4 ),
);

if ( ! fileperms( get_theme_root() ) || 755 != substr( sprintf( '%o', fileperms( get_theme_root() ) ), - 4 ) ) {

	$this_test['status'] = 'WARNING';

} else {

	$this_test['status'] = 'OK';

}

array_push( $tests, $this_test );

$this_test = array(
	'title'      => str_replace( ABSPATH, '', dirname( plugin_dir_path( $itsec_globals['plugin_file'] ) ) ),
	'suggestion' => '= 755',
	'value'      => substr( sprintf( '%o', fileperms( dirname( plugin_dir_path( $itsec_globals['plugin_file'] ) ) ) ), - 4 ),
);

if ( ! dirname( plugin_dir_path( $itsec_globals['plugin_file'] ) ) || 755 != substr( sprintf( '%o', fileperms( dirname( plugin_dir_path( $itsec_globals['plugin_file'] ) ) ) ), - 4 ) ) {

	$this_test['status'] = 'WARNING';

} else {

	$this_test['status'] = 'OK';

}

array_push( $tests, $this_test );

if ( defined( 'WP_CONTENT_DIR' ) ) {

	$wp_content_dir = WP_CONTENT_DIR;

} else {

	$wp_content_dir = ABSPATH . '/wp-content/';

}

$this_test = array(
	'title'      => str_replace( ABSPATH, '', $wp_content_dir ),
	'suggestion' => '= 755',
	'value'      => substr( sprintf( '%o', fileperms( $wp_content_dir ) ), - 4 ),
);

if ( ! fileperms( $wp_content_dir ) || 755 != substr( sprintf( '%o', fileperms( $wp_content_dir ) ), - 4 ) ) {

	$this_test['status'] = 'WARNING';

} else {

	$this_test['status'] = 'OK';

}

array_push( $tests, $this_test );

$wp_upload_dir = wp_upload_dir();

$this_test = array(
	'title'      => str_replace( ABSPATH, '', $wp_upload_dir['basedir'] ),
	'suggestion' => '= 755',
	'value'      => substr( sprintf( '%o', fileperms( $wp_upload_dir['basedir'] ) ), - 4 ),
);

if ( ! fileperms( $wp_upload_dir['basedir'] ) || 755 != substr( sprintf( '%o', fileperms( $wp_upload_dir['basedir'] ) ), - 4 ) ) {

	$this_test['status'] = 'WARNING';

} else {

	$this_test['status'] = 'OK';

}

array_push( $tests, $this_test );
//END FOLDERS

//BEGIN FILES
$this_test = array(
	'title'      => 'wp-config.php',
	'suggestion' => '= 444',
	'value'      => substr( sprintf( '%o', fileperms( ITSEC_Lib::get_config() ) ), - 4 ),
);

if ( ! fileperms( ITSEC_Lib::get_config() ) || 444 != substr( sprintf( '%o', fileperms( ITSEC_Lib::get_config() ) ), - 4 ) ) {

	$this_test['status'] = 'WARNING';

} else {

	$this_test['status'] = 'OK';

}

array_push( $tests, $this_test );

$this_test = array(
	'title'      => '.htaccess',
	'suggestion' => '= 444',
	'value'      => substr( sprintf( '%o', fileperms( ITSEC_Lib::get_htaccess() ) ), - 4 ),
);

if ( ! fileperms( ITSEC_Lib::get_htaccess() ) || 444 != substr( sprintf( '%o', fileperms( ITSEC_Lib::get_htaccess() ) ), - 4 ) ) {

	$this_test['status'] = 'WARNING';

} else {

	$this_test['status'] = 'OK';

}

array_push( $tests, $this_test );
//END FILES

?>

<table class="widefat">
	<thead>
	<tr class="thead">
		<th><?php _e('Relative Path', 'it-l10n-better-wp-security' ); ?></th>
		<th><?php _e('Suggestion', 'it-l10n-better-wp-security' ); ?></th>
		<th<?php _e('>Value', 'it-l10n-better-wp-security' ); ?></th>
		<th><?php _e('Result', 'it-l10n-better-wp-security' ); ?></th>
		<th style="width: 60px;"><?php _e('Status', 'it-l10n-better-wp-security' ); ?></th>
	</tr>
	</thead>
	<tfoot>
	<tr class="thead">
		<th><?php _e('Relative Path', 'it-l10n-better-wp-security' ); ?></th>
		<th><?php _e('Suggestion', 'it-l10n-better-wp-security' ); ?></th>
		<th><?php _e('Value', 'it-l10n-better-wp-security' ); ?></th>
		<th><?php _e('Result', 'it-l10n-better-wp-security' ); ?></th>
		<th style="width: 60px;"><?php _e('Status', 'it-l10n-better-wp-security' ); ?></th>
	</tr>
	</tfoot>
	<tbody>

	<?php
	foreach ( $tests as $this_test ) {

		echo '<tr class="entry-row alternate">';
		echo '	<td>' . $this_test['title'] . '</td>';
		echo '	<td>' . $this_test['suggestion'] . '</td>';
		echo '	<td>' . $this_test['value'] . '</td>';
		echo '	<td>' . $this_test['status'] . '</td>';
		echo '	<td>';

		if ( 'OK' == $this_test['status'] ) {

			echo '<div style="background-color: #22EE5B; border: 1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;</div>';

		} elseif ( 'FAIL' == $this_test['status'] ) {

			echo '<div style="background-color: #CF3333; border: 1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;</div>';

		} elseif ( 'WARNING' == $this_test['status'] ) {

			echo '<div style="background-color: #FEFF7F; border: 1px solid #E2E2E2;">&nbsp;&nbsp;&nbsp;</div>';

		}

		echo '	</td>';
		echo '</tr>';
	}
	?>
	</tbody>
</table>
