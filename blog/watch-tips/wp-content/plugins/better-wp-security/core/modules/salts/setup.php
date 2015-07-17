<?php

if ( ! class_exists( 'ITSEC_Salts_Setup' ) ) {

	class ITSEC_Salts_Setup {

		public function __construct() {

			global $itsec_setup_action;

			if ( isset( $itsec_setup_action ) ) {

				switch ( $itsec_setup_action ) {

					case 'activate':
						$this->execute_activate();
						break;
					case 'upgrade':
						$this->execute_upgrade();
						break;
					case 'deactivate':
						$this->execute_deactivate();
						break;
					case 'uninstall':
						$this->execute_uninstall();
						break;

				}

			} else {
				wp_die( 'error' );
			}

		}

		/**
		 * Execute module activation.
		 *
		 * @since 4.7.0
		 *
		 * @return void
		 */
		public function execute_activate() {

		}

		/**
		 * Execute module deactivation
		 *
		 * @since 4.7.0
		 *
		 * @return void
		 */
		public function execute_deactivate() {

		}

		/**
		 * Execute module uninstall
		 *
		 * @since 4.7.0
		 *
		 * @return void
		 */
		public function execute_uninstall() {

			$this->execute_deactivate();

			delete_site_option( 'itsec_salts' );

		}

		/**
		 * Execute module upgrade
		 *
		 * @return void
		 */
		public function execute_upgrade() {

		}

	}

}

new ITSEC_Salts_Setup();