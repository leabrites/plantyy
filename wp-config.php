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
define( 'DB_NAME', 'plantyy' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         '@0/$DGuo/ `e]*|H4.>-GWmjMGwLLUJG!zJ4:{EiZlTc=PG-7}nASh&Ua]~Do9UT' );
define( 'SECURE_AUTH_KEY',  'idsOO3>]KD~6y6mCM)o bS?Hh0PV&6rB,(]D|6Wkg{ATUA,m3 Gj&6OXTYox~n[h' );
define( 'LOGGED_IN_KEY',    '?SJgL~g4uFz0g6JcM:_CtlJ;cG:IzgK# fy]zz~G-<8kHGwo&bAbfS.#V2N}&BTX' );
define( 'NONCE_KEY',        '1>pqaG*V(`fJWD0=G(,nbH(:MaIn4U8ZA0-wCS{[y+s:GCvHUO!xEXsJ.W13d-//' );
define( 'AUTH_SALT',        'fJ%|[Twy39BOrE-Jj1udF65>UD8i,YZ8EQ1FgItZ1`G,QFi9<Kr|xaFsJW9NcQ@.' );
define( 'SECURE_AUTH_SALT', 'g[4tn2=6h3tPu| 7fQvn:4ILf^0(?K[dIf5,$,MpD]|3kYcuy EM(x.0f#~$Q|cC' );
define( 'LOGGED_IN_SALT',   ')Vi5+Tvy[}nBrPwjJDM nDR2 _`Ty?C:v#>%a6C%h?),,fojMk9s)2e zR4^G%{q' );
define( 'NONCE_SALT',       'vpzD6?#~J_F^88z|`m{t{xAaT|Ss*G^QR[8|rLgSXA]65UA,u>j)_fn XnzPLjsT' );

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
