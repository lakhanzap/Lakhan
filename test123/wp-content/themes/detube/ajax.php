<?php
/**
 * Front WordPress AJAX Process Execution.
 *
 * @package deTube
 *
 * @link http://codex.wordpress.org/AJAX_in_Plugins
 */

/**
 * Executing AJAX process.
 *
 * @since deTube 1.4
 */
define('DOING_AJAX', true );
define('WP_USE_THEMES', false);

/** Load WordPress Bootstrap */
require('../../../wp-load.php');

/** Allow for cross-domain requests (from the frontend). */
send_origin_headers();

// Require an action parameter
if ( empty( $_REQUEST['action'] ) )
	die( '0' );

@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
@header( 'X-Robots-Tag: noindex' );

send_nosniff_header();
nocache_headers();

// do_action( 'init' );
		
if ( is_user_logged_in() )
	do_action( 'wp_ajax_' . $_REQUEST['action'] ); // Authenticated actions
else
	do_action( 'wp_ajax_nopriv_' . $_REQUEST['action'] ); // Non-admin actions

// Default status
die( '0' );