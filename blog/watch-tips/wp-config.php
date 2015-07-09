<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, and ABSPATH. You can find more information by visiting
 * {@link http://codex.wordpress.org/Editing_wp-config.php Editing wp-config.php}
 * Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

define('WP_HOME','http://toolwatchapp.herokuapp.com/blog/watch-tips/');
define('WP_SITEURL','http://toolwatchapp.herokuapp.com/blog/watch-tips/');

$url = parse_url(getenv("CLEARDB_DATABASE_URL"));

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', substr($url["path"], 1));

/** MySQL database username */
define('DB_USER', $url["user"]);

/** MySQL database password */
define('DB_PASSWORD', $url["pass"]);

/** MySQL hostname */
define('DB_HOST', $url["host"]);

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'EuIFEh0ZW14PclBmsIqHle1L29+D51IALOI0GZchmupE977g5CuT3MTUwtuU');
define('SECURE_AUTH_KEY',  '+WOKm4lHcRU9m+anT9wJe3w2usGrukL45D41owMp3CPbNli9FDZ1yCp6jBZ1');
define('LOGGED_IN_KEY',    'vazS/KqUUbawLWMFVij1FHcR319ZtL1yLIbDbewRtMnGcjF3au5vjdN5FMWB');
define('NONCE_KEY',        '5MPs+DeLq8sdL0OEQ+Gb4dtvjAMcEltdI7clZoWgS6QE0atp/AsJGc7QdzwU');
define('AUTH_SALT',        '+tU0oLAJ3JN3F94om0ftonpG1b7bSwaY5VOylj9LhpbdI1K8F3OHEIM+dD42');
define('SECURE_AUTH_SALT', 'Wsvf4LVHeJwZvnQQSxlnBdAuEC1qez2pyJwNLAzaCqi+yuU+z/uXuPiSMmwO');
define('LOGGED_IN_SALT',   'Y8kyr5MmI2ereaJC/HBl8r4ok/IJO8Ncrc9HR6ryiTumzktMGOo98v6oZZuz');
define('NONCE_SALT',       '5HDyQSdNy19CnNKudJJRUEDKPMCMQiVEVKpE+I7+PvO5OKeJietFWOpj81Je');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
