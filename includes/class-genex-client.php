<?php
/**
 * The client-specific functionality of the plugin.
 *
 * @link       https://www.genexmarketing.com/
 * @since      1.0.0
 *
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/includes
 */

/**
 * The client-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and handles communication with the hub.
 *
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/includes
 * @author     Ravi Bhatia <your.email@example.com>
 */
class Genex_Client {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The URL of the hub site.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $hub_url    The URL of the hub site.
	 */
	private $hub_url;

	/**
	 * The API key for the hub site.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $api_key    The API key for the hub site.
	 */
	private $api_key;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->hub_url     = get_option( 'genex_hub_url' );
		$this->api_key     = get_option( 'genex_hub_api_key' );
	}

	/**
	 * Fetch banners from the hub site.
	 *
	 * @since    1.0.0
	 */
	public function fetch_banners() {
		$is_first_run = ! get_option( 'genex_alert_type' );
		$alert_type   = get_option( 'genex_alert_type', 'hub' );
		if ( ! $is_first_run && $alert_type === 'client' ) {
			if ( empty( $this->hub_url ) ) {
				error_log( 'Genex Alert Banner: Hub URL or API Key is missing.' );
				return;
			}
			$response = wp_remote_get(
				$this->hub_url . '/wp-json/genex/v1/alert-banners',
				array(
					'headers' => array(
						'Authorization' => 'Basic ' . base64_encode( get_bloginfo( 'name' ) . ':' . $this->api_key ),
					),
				)
			);
			if ( is_wp_error( $response ) ) {
				error_log( 'Error fetching banners: ' . $response->get_error_message() );
				return;
			}
			$body    = wp_remote_retrieve_body( $response );
			$banners = json_decode( $body, true );
			if ( empty( $banners ) ) {
				delete_option( 'genex_banners' );
				return;
			}
			update_option( 'genex_banners', $banners );
		}
	}

	/**
	 * Get banners from options table.
	 *
	 * @since    1.0.0
	 */
	public function get_banners() {
		return get_option( 'genex_banners', array() );
	}

	/**
	 * Schedule the banner sync.
	 *
	 * @since    1.0.0
	 */
	public function schedule_sync() {
		$is_first_run = ! get_option( 'genex_alert_type' );
		$alert_type   = get_option( 'genex_alert_type', 'hub' );
		if ( ! $is_first_run && $alert_type === 'client' ) {
			if ( ! wp_next_scheduled( 'genex_fetch_banners_event' ) ) {
				wp_schedule_event( time(), 'fifteen_minutes', 'genex_fetch_banners_event' );
			}
		}
	}

	/**
	 * Unschedule the banner sync.
	 *
	 * @since    1.0.0
	 */
	public function unschedule_sync() {
		$is_first_run = ! get_option( 'genex_alert_type' );
		$alert_type   = get_option( 'genex_alert_type', 'hub' );
		if ( ! $is_first_run && $alert_type === 'client' ) {
			$timestamp = wp_next_scheduled( 'genex_fetch_banners_event' );
			if ( $timestamp ) {
				wp_unschedule_event( $timestamp, 'genex_fetch_banners_event' );
			}
		}
	}

	/**
	 * Activate the client.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$is_first_run = ! get_option( 'genex_alert_type' );
		$alert_type   = get_option( 'genex_alert_type', 'hub' );
		if ( ! $is_first_run && $alert_type === 'client' ) {
			$plugin_client = new Genex_Client( 'genex-alert-banner', '1.0.0' );
			$plugin_client->schedule_sync();
		}
	}

	/**
	 * Deactivate the client.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {
		$is_first_run = ! get_option( 'genex_alert_type' );
		$alert_type   = get_option( 'genex_alert_type', 'hub' );
		if ( ! $is_first_run && $alert_type === 'client' ) {
			$plugin_client = new Genex_Client( 'genex-alert-banner', '1.0.0' );
			$plugin_client->unschedule_sync();
		}
	}
}
add_action( 'genex_fetch_banners_event', 'genex_fetch_banners_callback' );
function genex_fetch_banners_callback() {
	$is_first_run = ! get_option( 'genex_alert_type' );
	if ( ! $is_first_run ) {
		$plugin_client = new Genex_Client( 'genex-alert-banner', '1.0.0' );
		$plugin_client->fetch_banners();
	}
}
