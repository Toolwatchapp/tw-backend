<?php

class Ithemes_Sync_Verb_ITSEC_Get_Everything extends Ithemes_Sync_Verb {

	public static $name        = 'itsec-get-everything';
	public static $description = 'Retrieve iThemes Security Status and other information.';

	private $default_arguments = array();

	public function run( $arguments ) {

		global $itsec_sync;

		$modules        = $itsec_sync->get_modules();
		$module_results = array();

		//return $modules;

		foreach ( $modules as $name => $module ) {

			if ( isset( $module['verbs'] ) && isset( $module['path'] ) && isset( $module['everything'] ) ) {

				$everything = array();

				if ( is_array( $module['everything'] ) ) {

					foreach ( $module['everything'] as $item ) {

						if ( isset( $module['verbs'][ $item ] ) ) {
							$everything[] = $item;
						}

					}

				} elseif ( isset( $module['verbs'][ $module['everything'] ] ) ) {

					$everything[] = $module['everything'];

				}

				foreach ( $everything as $verb ) {

					$class = $module['verbs'][ $verb ];

					if ( ! class_exists( $class ) ) {

						require( trailingslashit( $module['path'] ) . 'class-ithemes-sync-verb-' . $verb . '.php' );

					}

					$obj = new $class;

					$module_results[ $name ][ $verb ] = $obj->run( array() );

				}

			}

		}

		return array_merge( array(
			                    'api' => '1',
		                    ),
		                    $module_results
		);

	}

}
