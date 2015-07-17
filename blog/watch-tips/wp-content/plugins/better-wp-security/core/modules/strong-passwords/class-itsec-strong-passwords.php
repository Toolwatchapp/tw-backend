<?php

class ITSEC_Strong_Passwords {

	private
		$settings,
		$module_path;

	function run() {

		$this->settings    = get_site_option( 'itsec_strong_passwords' );
		$this->module_path = ITSEC_Lib::get_module_path( __FILE__ );

		//require strong passwords if turned on
		if ( isset( $this->settings['enabled'] ) && $this->settings['enabled'] === true ) {

			add_action( 'user_profile_update_errors', array( $this, 'enforce_strong_password' ), 0, 3 );
			add_action( 'validate_password_reset', array( $this, 'enforce_strong_password' ), 10, 2 );

			if ( isset( $_GET['action'] ) && ( $_GET['action'] == 'rp' || $_GET['action'] == 'resetpass' ) && isset( $_GET['login'] ) ) {
				add_action( 'login_head', array( $this, 'enforce_strong_password' ) );
			}

			add_action( 'admin_enqueue_scripts', array( $this, 'login_script_js' ) );
			add_action( 'login_enqueue_scripts', array( $this, 'login_script_js' ) );

		}

	}

	/**
	 * Require strong passwords
	 *
	 * Requires new passwords set are strong passwords
	 *
	 * @param object $errors WordPress errors
	 *
	 * @return object WordPress error object
	 *
	 **/
	function enforce_strong_password( $errors ) {

		//determine the minimum role for enforcement
		$min_role = isset( $this->settings['roll'] ) ? $this->settings['roll'] : 'administrator';

		//all the standard roles and level equivalents
		$available_roles = array(
			'administrator' => '8',
			'editor'        => '5',
			'author'        => '2',
			'contributor'   => '1',
			'subscriber'    => '0'
		);

		//roles and subroles
		$rollists = array(
			'administrator' => array( 'subscriber', 'author', 'contributor', 'editor' ),
			'editor'        => array( 'subscriber', 'author', 'contributor' ),
			'author'        => array( 'subscriber', 'contributor' ),
			'contributor'   => array( 'subscriber' ),
			'subscriber'    => array(),
		);

		$password_meets_requirements = false;
		$args                        = func_get_args();
		$user_id                     = isset( $args[2]->user_login ) ? $args[2]->user_login : false;

		if ( $user_id === false ) { //try to get a working user ID

			if ( isset( $args[1] ) && isset( $args[1]->ID ) ) {

				if ( isset( $args[1]->user_login ) ) {

					$user_id = $args[1]->user_login;

				} else {

					$user_id = $args[1]->get( 'user_login' );
				}

			}

		}

		if ( $user_id ) { //if updating an existing user

			if ( $user_info = get_user_by( 'login', $user_id ) ) {

				foreach ( $user_info->roles as $capability ) {

					if ( isset( $available_roles[ $capability ] ) && $available_roles[ $capability ] >= $available_roles[ $min_role ] ) {
						$password_meets_requirements = true;
					}

				}

			} else { //a new user

				if ( ! empty( $_POST['role'] ) && ! in_array( $_POST["role"], $rollists[ $min_role ] ) ) {
					$password_meets_requirements = true;
				}

			}

		}

		if ( $password_meets_requirements === true ) {

			add_action( 'shutdown', array( $this, 'shut_down_js' ) );

		}

		if ( ! isset( $_GET['action'] ) ) {

			//add to error array if the password does not meet requirements
			if ( $password_meets_requirements && ! $errors->get_error_data( 'pass' ) && isset( $_POST['pass1'] ) && trim( strlen( $_POST['pass1'] ) ) > 0 && isset( $_POST['password_strength'] ) && $_POST['password_strength'] != 'strong' ) {
				$errors->add( 'pass', __( '<strong>ERROR</strong>: You MUST Choose a password that rates at least <em>Strong</em> on the meter. Your setting have NOT been saved.', 'it-l10n-better-wp-security' ) );
			}

		}

		return $errors;
	}

	/**
	 * Enqueue script to check password strength
	 *
	 * @return void
	 */
	public function login_script_js() {

		global $itsec_globals;

		if ( $this->settings['enabled'] === true ) {

			wp_enqueue_script( 'itsec_strong_passwords', $this->module_path . 'js/strong-passwords.js', array( 'jquery' ), $itsec_globals['plugin_build'] );

			//make sure the text of the warning is translatable
			wp_localize_script( 'itsec_strong_passwords', 'strong_password_error_text', array( 'text' => __( 'Sorry, but you must enter a strong password.', 'it-l10n-better-wp-security' ) ) );

		}

	}

	/**
	 * Ad js for reset password page
	 *
	 * @since 4.0.10
	 *
	 * @return void
	 */
	public function shut_down_js() {

		?>

		<script type="text/javascript">
			jQuery( document ).ready( function () {
				jQuery( '#resetpassform' ).submit( function () {
					if ( ! jQuery( '#pass-strength-result' ).hasClass( 'strong' ) ) {
						alert( '<?php _e( "Sorry, but you must enter a strong password", "ithemes-security" ); ?>' );
						return false;
					}
				} );
			} );
		</script>

	<?php
	}

}