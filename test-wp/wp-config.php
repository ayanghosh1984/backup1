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
define( 'DB_NAME', 'test_wp' );

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
define( 'AUTH_KEY',         '(6BbUa=]oBES2A,RV?&kjk;7Bl?s==t`*1=BWWVVOFix{xc!+Kf8|=82WlmS^cL?' );
define( 'SECURE_AUTH_KEY',  'Z(yUI.,O^^U%.qr.u,!ZWTXjvWgSvCc.cR ,3ETCFl|Pu),6*W5NU1AUJ6O+J%&#' );
define( 'LOGGED_IN_KEY',    'h*`Jw^RA*({3U[wA/5m0n5- }C/Q/;)gK.3W61@M+}kJ9B+ddqYQgCCl jSfmoEO' );
define( 'NONCE_KEY',        'QlyHOqcTf!;0&G;ii;Dz_HI[TgpNai BmGf3F=FSn.A/5<-w#w]kRL/Gzl5t^ +F' );
define( 'AUTH_SALT',        'uIELp*e&#%o)Yc&}|p+Kh{:4o.8=l5~X4nFq,x(u[(1gMoyBwgU8!oKP;}j[`1an' );
define( 'SECURE_AUTH_SALT', ')iC*sRNg6hAu=+KZr*XaKNi1ycSkILidf * .F/j{dPB]?&2m_%_Exm>_lx]F2xe' );
define( 'LOGGED_IN_SALT',   't!^Qt-uWr)wrA9@}v#&s2O{j1N6&r4z9E7E8Vl?$^Fu[y[j+dK,YKBA<{7p v<dU' );
define( 'NONCE_SALT',       'wgAG[R>bJ>IF[)ql9(L3|(bc}U.jXjLmdBvBacT2EV:63a?/QV8W}It13-GI@H]@' );

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
