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

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'test123');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

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
define('AUTH_KEY',         '<3AN9Q=Oq7-0&*a12;NyhJ)1tAl$:;9Y<ACm=1Il.+*z&*A@S-hR-35FKMo~6;hF');
define('SECURE_AUTH_KEY',  '<[c-:jLn(s>Dz)!}4b$njn7?+9]$p,|&3PC|xv+Xb^gY}@hWR/Wt#)NRD`(4?b&<');
define('LOGGED_IN_KEY',    'b^(I[U/AIrj)yv+vHs%#QE>^Pca;>sPd0R<|g@sJl)Doj)+5S!j$?^MM/Abr|-y7');
define('NONCE_KEY',        'h_,M&=DlpzEY+N+skAATk~H,vVvSt~q#A>2|iaXVHLKaS:Ry|@|}N4*A*LjER+`k');
define('AUTH_SALT',        ' |G2%cB)i,(0}#z:+9f1tpMH#7t&E<dYe8B-]U3zddUTjY55+-TdK,g-?=hP/ur|');
define('SECURE_AUTH_SALT', 'wI,jE+Zz92PVO(MxzHxwtBKntZq2<exiq$F^}jXx?]q)@s{]}:*Yo+U$9EJ~F#c&');
define('LOGGED_IN_SALT',   '?&Ih4Ezj>AlpU$Z-QJ_@SFcD/dhO.} x<-DhO-+l%=V_LMGkc0wMScP6c(gIMH&m');
define('NONCE_SALT',       'Ou#+Xk9Bj V0we`?SO,#/qp6_&@oMzH|xT4=EXap/-*Pc<*$%^QXNfasg8Y&u{FA');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
