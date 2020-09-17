<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '6Ln/KuJKgJjFn9D3u01D+ZCMF3knpdSk1woLqLSsP9h8znJQlZcdc5A1iFlQOAZh5t0szjlUHLqO6S7xht5dbQ==');
define('SECURE_AUTH_KEY',  'ncFb8NjXBfKY5Fd4UGO9saioPcqLaRuKnmn/kyceqmJuI+3S8xB50z/JTzu/sfKVplV1FgoP8rzX2Vx9Je/JvA==');
define('LOGGED_IN_KEY',    '6BJUnzWYC/iwWOjgV1DIy/0hcp9oMCxJtVhhJ6ktpS07ypEmd8hgZNY/jgXmzoG3Kkgqq+3wnStsHiXWFhbfhA==');
define('NONCE_KEY',        'cY+fctlne0WO6FtGdx+z8W/SS5uHldMRSqLU3/QO1sFoZZYFqFkxpiejfshEtnjEe0moD4yFSQ4qTryrq8unCA==');
define('AUTH_SALT',        'Tgny7P5/iaexFMBPDBEX21gw+2ecyQXEApXBaah3CwC7290DjE9GMhOLy5r2t1svycv5RAVQ1GeOl031DcRqxQ==');
define('SECURE_AUTH_SALT', 'k7LbOQEPxF/JDAk7x17V82NRW1IiGGZtMRlgqZEQNUrkefD1ki0JOuL3ZMmSgmB8L4HzDj0xdZ6X/9Em3+gmnQ==');
define('LOGGED_IN_SALT',   'S2XC28uz4haJ6DbmxXTrGG84cF8CNNAW8c3lIc/bJEkdDk6zZA/BKp+eJjamiQia1XgRzfZH9wHa2V2HVz5S/g==');
define('NONCE_SALT',       'pGhn+a7RnMPlhTPWbDleW4vfPoX3o0hmLToMIRruL8R1YxxHFijbUVYw6UiysgFuHuIltS2o4SbFUEYIbekR5g==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
