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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_app_challenge' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',         'S`jTXFQstFMZ=S3q.&h;%-XL-c-O7q_`4ZY6e=hD+1 QxP9*ud_1;1QOsbx!n:!_' );
define( 'SECURE_AUTH_KEY',  '(lNZAG%UB?BAPv-z@H(I]$DdnbY$Aeg+4|C%qnVdOn,eeM{?-}L&ccaQ%>)c1%b(' );
define( 'LOGGED_IN_KEY',    'v!;%[kpY~$IhM|mH/Io9xd-jj}4je&k@vKGQG:S$v~,y(OJ?s&QrXrxcbjy!yqqG' );
define( 'NONCE_KEY',        '5rB8/W/O!?Cj2}hj+!nExuvv%q{qy.i0D>R]27uez,)Fp>B>jBju]B@Twt)(uVT0' );
define( 'AUTH_SALT',        'apXK&_AJ-jT4^F|YFC!&L/gQg(zkMYTp.c4l<& N^7.~.p$pBzO12XL#/Q~I&VPJ' );
define( 'SECURE_AUTH_SALT', '$C[}^R#s@3Bs-A1$Nr42PY~.5fo_tkeL;>hq_w,dWpDR@{joBsTHqAt[uQ+,pxcA' );
define( 'LOGGED_IN_SALT',   'qFZi*4uaq7j5VY0HB9#<dOQSe@/SY#6cJnF$S8ue,t)T:R0x;4F/w.mH.)x-is-b' );
define( 'NONCE_SALT',       '9$8n@~[[l!yg#+[);3J:<%nk3`z~M9Tzgu@$Lovc6<SEzY].[ngIh}o@+>(s^_xx' );

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
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
