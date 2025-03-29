<?php
/**
 * Plugin Name: Genex Alert Banner
 * Plugin URI:  https://www.genexmarketing.com/
 * Description: Displays alert banners on your website for logged-in users.
 * Version:     1.0.0
 * Author:      Ravi Bhatia
 * Author URI:  https://www.genexmarketing.com/
 * Text Domain: genex-alert-banner
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 */
function activate_genex_alert_banner() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-genex-alert-banner.php';
	Genex_Alert_Banner::activate();
}

/**
 * The code that runs during plugin deactivation.
 */
function deactivate_genex_alert_banner() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-genex-alert-banner.php';
	Genex_Alert_Banner::deactivate();
}

register_activation_hook( __FILE__, 'activate_genex_alert_banner' );
register_deactivation_hook( __FILE__, 'deactivate_genex_alert_banner' );

/**
 * Defined Initernationalization, admin/public hooks
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-genex-alert-banner.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_genex_alert_banner() {

	$plugin = new Genex_Alert_Banner();
	$plugin->run();

}
run_genex_alert_banner();

/**
 * Custom Cron-ob interval to be used in Client Sites
 */
add_filter('cron_schedules', 'genex_add_cron_interval');
function genex_add_cron_interval($schedules)
{
    $schedules['fifteen_minutes'] = array(
        'interval' => 900,
        'display' => __('Once every fifteen minutes')
    );
    return $schedules;
}
