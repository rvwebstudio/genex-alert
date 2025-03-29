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

// Delete any options or custom tables here if needed.
// For example:
// delete_option( 'genex_alert_banner_option_name' );
