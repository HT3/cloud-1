<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// $onGae is true on production.
if (isset($_SERVER['GAE_ENV'])) {
    $onGae = true;
} else {
    $onGae = false;
}

// Cache settings
// Disable cache for now, as this does not work on App Engine for PHP 7.2
define('WP_CACHE', false);

// Disable pseudo cron behavior
define('DISABLE_WP_CRON', true);

// Determine HTTP or HTTPS, then set WP_SITEURL and WP_HOME
if ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)) {
    $protocol_to_use = 'https://';
} else {
    $protocol_to_use = 'http://';
}
if (isset($_SERVER['HTTP_HOST'])) {
    define('HTTP_HOST', $_SERVER['HTTP_HOST']);
} else {
    define('HTTP_HOST', 'localhost');
}
define('WP_SITEURL', $protocol_to_use . HTTP_HOST);
define('WP_HOME', $protocol_to_use . HTTP_HOST);

// ** MySQL settings - You can get this info from your web host ** //
if ($onGae) {
    /** The name of the Cloud SQL database for WordPress */
    define('DB_NAME', 'cloud-1');
    /** Production login info */
    define('DB_HOST', ':/cloudsql/cloud-1-279002:us-central1:cloud-1');
    define('DB_USER', 'root');
    define('DB_PASSWORD', 'Lolajones1$');
} else {
    /** The name of the local database for WordPress */
    define('DB_NAME', 'cloud-1');
    /** Local environment MySQL login info */
    define('DB_HOST', '127.0.0.1');
    define('DB_USER', 'root');
    define('DB_PASSWORD', 'Lolajones1$');
}

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
define('AUTH_KEY',         'tbbnnVXIelHSUlUbSEOXF3gJ1z3IMLohyQe0/Yc/EA9wGhu8MaXyrUaI2d3dZBBJaW2AzzZbuhkQ3EHA');
define('SECURE_AUTH_KEY',  'tvsorQH4RLfTeEEJBqPxt8LxqCUHBqwsRYoJpvhbz5KsdaT+YVmOI0lwxkdXaB/+FSFpevhuaISIW8Xy');
define('LOGGED_IN_KEY',    'F/YyPhJ53YZgkvn0ZqL4PlU2dGtOzkriwC4qtC8ZUIUYYRkQzmEh1GddKFzjiX/euNfkfNiP7CVzLme3');
define('NONCE_KEY',        'wNQNkCUxSuR+MIVntpifsgLDpHwB8g/5U1xDTJNL6UXf60ygfH3nRAuOgzFeRpe1ztgxx/wj19rXTbgR');
define('AUTH_SALT',        'sOaCVm24hr2KgJJ6nWTNuafX4XhF/VUvDXgCJJt0z/W2ekKBOifVdN9Jdu0rGAOHEAXP15RaT7iFolvh');
define('SECURE_AUTH_SALT', 'XlHGKa7NwUawxQFhQYuylQy3hqFvs/TSuObVDvhUBsO7/UHrZMwIxu6E9WIokEnIH/JgrkVq3xGbdh6r');
define('LOGGED_IN_SALT',   'OMuw8AaXe/VmbNtIp3nwIU0H9iHh70qHUrb9wixk8p98O7i5010ZX5c4mxiAOK03Q6NHldmMBYjeFBd3');
define('NONCE_SALT',       'dsWDHQJFdAz8A0Cz2dzHZImH/YsozAJdBAVxHZ5hOMgiBTyXS6UpUR3WkySijKI+EMWq/1cnNvCUHNMV');

/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', !$onGae);

// /** Enable W3 Total Cache */
// define('WP_CACHE', true); // Added by W3 Total Cache

// define( 'FS_METHOD', 'direct' );

/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
    define('ABSPATH', dirname(__FILE__) . '/wordpress/');
}

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
