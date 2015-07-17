<?php
/**
 * Handles the abstraction of sync integration.
 *
 * Registers modules with sync verbs and loads appropriate verb classes where applicable.
 *
 * @package iThemes_Security
 *
 * @since   4.0.0
 */
final class ITSEC_Sync {

	/**
	 * The module's that have registered with sync
	 *
	 * @since  4.1.0
	 * @access private
	 * @var array
	 */
	private $sync_modules;

	/**
	 * Loads sync modules
	 *
	 * Executes primary file actions at plugins_loaded.
	 *
	 * @since  4.1.0
	 *
	 * @return ITSEC_Sync
	 */
	public function __construct() {

		$this->sync_modules = array(); //array to hold information on modules using this feature

		add_action( 'plugins_loaded', array( $this, 'register_modules' ), 20 );
		add_action( 'ithemes_sync_register_verbs', array( $this, 'ithemes_sync_register_verbs' ) );

	}

	/**
	 * Returns all modules registered with Sync.
	 *
	 * Returns an array of all modules containing sync verbs.
	 *
	 * @since 4.1.0
	 *
	 * @return array sync module registrations
	 */
	public function get_modules() {

		return $this->sync_modules;

	}

	/**
	 * Register verbs for iThemes Sync.
	 *
	 * Registers all verbs for a given module.
	 *
	 * @since 4.1.0
	 *
	 * @param object $api iThemes Sync Object
	 *
	 * @return void
	 */
	public function ithemes_sync_register_verbs( $api ) {

		foreach ( $this->sync_modules as $module ) {

			if ( isset( $module['verbs'] ) && isset( $module['path'] ) ) {

				foreach ( $module['verbs'] as $name => $class ) {

					$api->register( $name, $class, trailingslashit( $module['path'] ) . 'class-ithemes-sync-verb-' . $name . '.php' );

				}

			}

		}

		$api->register( 'itsec-get-everything', 'Ithemes_Sync_Verb_ITSEC_Get_Everything', dirname( __FILE__ ) . '/class-ithemes-sync-verb-itsec-get-everything.php' );

	}

	/**
	 * Register modules that will use the sync service.
	 *
	 * Executes a filter that allows modules to register themselves for iThemes Sync integration.
	 *
	 * @since 4.1.0
	 *
	 * @return void
	 */
	public function register_modules() {

		$this->sync_modules = apply_filters( 'itsec_sync_modules', $this->sync_modules );

	}

}