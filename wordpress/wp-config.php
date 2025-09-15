<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'zistak_codeban' );

/** Database username */
define( 'DB_USER', 'zistak_codeban' );

/** Database password */
define( 'DB_PASSWORD', 'SLafMbtt5Gh7Ys8CX6zg' );

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
define( 'AUTH_KEY',         'eXM9Dn2sxImURql@,QPUrBaI0K{kf&6VZYb[p(YiF5:o}#O#5V?RaG<6{93h!)ul' );
define( 'SECURE_AUTH_KEY',  '6:Fz#csb5}J{bQQ=D0eTn>mBa@N+={O6-7)#43+r2XSWK`G&pDu]kU(Oie~G9DB?' );
define( 'LOGGED_IN_KEY',    'J|hQ:,aNH06o+ORzhHgS/H_W]q{!-an=!CP{`@E.;!8N- _!}*`igou9Ic5Fvz:y' );
define( 'NONCE_KEY',        '12T8iF,kSb,got$o]cpE4|pE+l6p-WO6M)QlR)v!(TUpP3|`L~>1<.V?Jhf0S@P&' );
define( 'AUTH_SALT',        '=w!~0<Rw{Uf(6.b[2=lKi=ICUk03Yz5em?#NU(ho(//7z^?Cv{es<`c6Cm;6.F@U' );
define( 'SECURE_AUTH_SALT', '=YO`*J>]=*-_G*[fHf7SAiaVOfTW7=/iby{kNuD :L|be!a%G*lCg,HUcZQ]kO]x' );
define( 'LOGGED_IN_SALT',   'eK72&gbyF4+dWF_qA*hMKRu:!2Xc5yT221J>Jh_D9W)?o+?nAA:TG2|]vP#Qqe-H' );
define( 'NONCE_SALT',       '6+O$&0;z@eiz=jMl![y42QSqAA(=ZQ38MsO{B.6W0Q:}@V5%#]G$VQlDe2:d.*m+' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
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
