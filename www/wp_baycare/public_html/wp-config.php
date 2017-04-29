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

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wp_baycare' );

/** MySQL database username */
define( 'DB_USER', 'wp' );

/** MySQL database password */
define( 'DB_PASSWORD', 'wp' );

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
define('AUTH_KEY',         '!ANiawR@^qDtkSVD+|NSb|u7_fkhDT|<h{@4Q<AaDC~tq!*/o5w$G|6mGr}64GJw');
define('SECURE_AUTH_KEY',  'fQh7J0e8Ud?0lKCb`uJkyMCFw17D|]ryk9Jy,NZ-?d0xG3J-b^uENw6Rk21Bq8rr');
define('LOGGED_IN_KEY',    '4H2v9Q/(f2O!r)Mcu}r*^F8gtTg&G/4}$.GL2c7#iC$+Z8>Pb{I)m: ;PpV;0CSS');
define('NONCE_KEY',        'V|[|&F4|[%Wb)D3kd9=e%,l`*tP G!]|,+u{_GZ|]&u#]}Ya?+m%GxHL`7~s1-y#');
define('AUTH_SALT',        '?.BiO.8BHsV~#?%Xo()JE^<8PxefHqB63o2CI>/,57D@r#EX+sK4bO5o-j1Zv<u~');
define('SECURE_AUTH_SALT', '8tl7v?P5^`>~flI`}nC$>0n6Ms-!Vm+y_|mprc]AV*u>+!&G>lez.#BDY]aLynz}');
define('LOGGED_IN_SALT',   'sE~lw.Rwu%X6cVxh:5!r]{e/#C[[?t@8f^{x}g,#0HoS~(Y*mr`;P(P(kg+U`1|4');
define('NONCE_SALT',       '&fA[sfM<|Kt|t}]niesB*A><A]1?QWJE&rPnel79{{E9ke^}No314>N}Z:S5lX~0');


/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


define( 'WP_DEBUG', true );


/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
