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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_db' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '6v<V;;H&x%8eqcP~;51)SFxf-6h5;S~14FDmeC3d:Ro[.b>jH;Bg56$~~<IRkd5M' );
define( 'SECURE_AUTH_KEY',  'Ub_~UiJ7q|kce|L*KgDtpkV5xE%)7Is1e[8Wh0&a#,/s.!/}Bc?s}$y=NY$=QVMX' );
define( 'LOGGED_IN_KEY',    '$1*(-K<lt,gz.o_YsCu?PMy8v]7(<,ANG@6#z`.m~ah(n*m;oEE~oUSx$6Rvc*YX' );
define( 'NONCE_KEY',        'zdCY/Oq~LiJ.AcFM4;=;f@/7c|o~q@_)I~.mfc%8{3EK6(?WHt<T9MG6G`}p>:Va' );
define( 'AUTH_SALT',        '^%F(!+=:nDBMDZ%T}^c+ydF7??4},-^6OPEz %o&;JfRs8<,!:5:gYNB,gZ3?iK^' );
define( 'SECURE_AUTH_SALT', 'mU&ID3{5v6bN+Mu?173[cn]);|_jOI)9klAZ:F9d.4(ihQ-Zzy^v][kF`jJ.TnXL' );
define( 'LOGGED_IN_SALT',   '6?b6JhG~1gkdfkh58t`bZM5|!n<c3<Gi.@S5[|{-gS!EG| FCC,1{?Hv}Ul8I3;D' );
define( 'NONCE_SALT',       'wISTyl$1zJ)F [052Lt`8VC3CmfRG Y0^,Y_v>|-hRIpj^HIs}<jcNw%IG` aANc' );

/**#@-*/

/**
 * WordPress Database Table prefix.
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

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
