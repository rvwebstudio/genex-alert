<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    Genex_Alert_Banner
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Unschedule the banner sync.
$timestamp = wp_next_scheduled( 'genex_fetch_banners_event' );
if ( $timestamp ) {
	wp_unschedule_event( $timestamp, 'genex_fetch_banners_event' );
}
// We can add more options here in future.

