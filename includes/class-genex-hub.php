<?php

/**
 * The hub-specific functionality of the plugin.
 *
 * @link       https://www.genexmarketing.com/
 * @since      1.0.0
 *
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/includes
 */

/**
 * The hub-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and registers the custom post type.
 *
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/includes
 * @author     Ravi Bhatia <your.email@example.com>
 */
class Genex_Hub {

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
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the REST API endpoints.
	 *
	 * @since    1.0.0
	 */
	public function register_api_endpoints() {
		register_rest_route(
			'genex/v1',
			'/alert-banners',
			array(
				'methods'             => 'GET',
				'callback'            => array( $this, 'get_alert_banners' )
			)
		);
	}

	/**
	 * Get all active alert banners.
	 *
	 * @since    1.0.0
	 * @param WP_REST_Request $request The request object.
	 * @return WP_REST_Response The response object.
	 */
	public function get_alert_banners( WP_REST_Request $request ) {
		$args    = array(
			'post_type'      => 'alert_banner',
			'posts_per_page' => - 1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'status',
					'value'   => 'active',
					'compare' => '=',
				),
				array(
					'key'     => 'start_datetime',
					'value'   => date( 'Y-m-d H:i' ),
					'compare' => '<=',
					'type'    => 'DATETIME',
				),
				array(
					'key'     => 'end_datetime',
					'value'   => date( 'Y-m-d H:i' ),
					'compare' => '>=',
					'type'    => 'DATETIME',
				),
			),
		);
		$query   = new WP_Query( $args );
		$banners = array();
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				$banners[] = array(
					'id'             => get_the_ID(),
					'title'          => get_the_title(),
					'content'        => get_the_content(),
					'start_datetime' => get_post_meta( get_the_ID(), 'start_datetime', true ),
					'end_datetime'   => get_post_meta( get_the_ID(), 'end_datetime', true ),
					'status'         => get_post_meta( get_the_ID(), 'status', true ),
				);
			}
			wp_reset_postdata();
		}
		return rest_ensure_response( $banners );
	}
}
