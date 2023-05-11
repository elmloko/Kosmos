<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('WP_CACHE', true);
define( 'WPCACHEHOME', 'C:\laragon\www\Kosmos\wp-content\plugins\wp-super-cache/' );
define( 'DB_NAME', 'Kosmos' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

if ( !defined('WP_CLI') ) {
    define( 'WP_SITEURL', $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
    define( 'WP_HOME',    $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] );
}



/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'CykZy8SrIIQ1dz3qXEdQgHY3Qc4af0t1xOeHVuyAE8TNfilk1QQYnmDArYF1RpYZ' );
define( 'SECURE_AUTH_KEY',  'QzAaiLIMQGsiZVFOhghdIwIuA5vsyJpCnB0ruMEPw3mjq71dFV24Ma0qiLsTBejV' );
define( 'LOGGED_IN_KEY',    'N8MIszXduKxdOjbAyengPu0dOIXxLulGu3fyuB2YnJ0GR2N3TnlQZIt6WFFGaxab' );
define( 'NONCE_KEY',        'ngOFAk4SdWt445nQPkwHTVn6o71c4iCgSaqZoefwhAltDf7gSNE9rwzPvdhgsjK2' );
define( 'AUTH_SALT',        'sHiK7rETMCHqBki5jVw4il1DDEy8BlzsR5QFCSy2e0Sp5HroROk3s8GfZGNuJEwU' );
define( 'SECURE_AUTH_SALT', 'BMtYpQWksDHZI3GyIlD0ZvOTduEd3OR6GWGoEofKbqsukHwLIxjSjsamX2yinr44' );
define( 'LOGGED_IN_SALT',   'f98k7BI0Z9Z69fO2jgB02E3RtvfyKvQk7AMmZX3qapmHgVC5AQFkLAF0RgeJ2NMg' );
define( 'NONCE_SALT',       'bXPinb5Q1xOyup47YQyunFzwdLS8w3O6fAHZTXzaaCX5X2BrSNR4pYn9YptiPEY8' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
