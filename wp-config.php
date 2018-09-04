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
define('AUTH_KEY',         '4D6EusvlSzMAbnvjfnjQGKW3cwrFjE2P4y/Rll4Fkg1EQ5fypc0q3Z34IwrwmW6X/wWCPGxHhV2rTj+13r1AHw==');
define('SECURE_AUTH_KEY',  'Q921DGvCJt5CbkHl7svk8qqkHLugeFmfs6Z658GwjMwyGhoV59xC17uJDVf5w84R1j+yYvWySzlUNqm+KPLapQ==');
define('LOGGED_IN_KEY',    'e8ydB5WRD2019dLtxp6ND9Mo0HrTZ6s0f0ZIlVG8CiyZBWSjiRWzEVmUQ0c0kstc24BbWRwoe5qJ1lVo4usMPQ==');
define('NONCE_KEY',        'kkViEfBtMKhZzp5xRYxXVUG/Cb5QrK17gjavP/pivPNc7vseyxHcUQVkVK4fhbnkcOctutx08N7P1HbJ4vxdog==');
define('AUTH_SALT',        'XuDeeZ72Is2RzX6XXWDbvKAysJVL7KWPCtd4MIIh65RjWUegKqBH3tb07hq6bXGgnKLeX1rGzJaXLW6FVcGz4A==');
define('SECURE_AUTH_SALT', 'se8mffP41oQ2YKKkMl5cSmyehAO3n7OH7SICJW2YWQrUKmlvvf5JqSQUBvVN6eCkQFFctcPAFd8NgcuNeE3OGQ==');
define('LOGGED_IN_SALT',   'EZOvAob8fPf4jtpwPmzgkQgU0gklu0nFOaz1M0tn7jTagezl3J+njU2ru5owI5+8gRwIwerfRayllsDjCQ0UiQ==');
define('NONCE_SALT',       'cj7Ita/ox6081AwAfryOeSdS0pDIDuKqgPD95H/LZN8QtBL0hVYatl25pEd1zMc9RKCrxI2liXEUVJcZiOoNqA==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';




/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
