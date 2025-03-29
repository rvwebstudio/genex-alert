<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://www.genexmarketing.com/
 * @since      1.0.0
 *
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and registers hooks for
 * enqueuing the public-facing stylesheet and JavaScript.
 *
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/public
 * @author     Ravi Bhatia <your.email@example.com>
 */
class Genex_Frontend {

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
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'public/css/public-styles.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'public/js/public-scripts.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Display the alert banner on the front-end.
	 *
	 * @since    1.0.0
	 */
	public function display_banner() {
		// Only show to logged-in users.
		if ( ! is_user_logged_in() ) {
			return;
		}

		$is_first_run = ! get_option( 'genex_alert_type' );
		$alert_type   = get_option( 'genex_alert_type', 'hub' );
		if ( ! $is_first_run ) {
			if ( $alert_type === 'hub' ) {
				
				$query = $this->get_hub_active_banners_query();
				if ( $query->have_posts() ) {
					$active_banners = array();
					while ( $query->have_posts() ) {
						$query->the_post();
						$active_banners[] = get_post();
					}
					wp_reset_postdata();

					// Display only the most recent banner.
					$most_recent_banner = reset( $active_banners );
					$start_datetime     = get_post_meta( $most_recent_banner->ID, 'start_datetime', true );
					$end_datetime       = get_post_meta( $most_recent_banner->ID, 'end_datetime', true );
					$start_datetime_formatted = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $start_datetime ) );
					$end_datetime_formatted   = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $end_datetime ) );

					// Display the banner.
					echo '<div id="genex-alert-banner" class="genex-alert-banner">';
					echo '<p>' . get_the_content( null, false, $most_recent_banner ) . '</p>';
					echo '<p class="genex-alert-banner-date">Start Date: ' . esc_html( $start_datetime_formatted ) . ' - End Date: ' . esc_html( $end_datetime_formatted ) . '</p>';
					if ( count( $active_banners ) > 1 ) {
						echo '<a href="#">View all active messages</a>';
					}
					echo '</div>';
				}
			} elseif ( $alert_type === 'client' ) {
				$plugin_client = new Genex_Client( $this->plugin_name, $this->version );
				$banners       = $plugin_client->get_banners();

				if ( ! empty( $banners ) ) {
					// Find the most recent active banner.
					$most_recent_banner = null;
					foreach ( $banners as $banner ) {
						if ( $banner['status'] === 'active' && strtotime( $banner['start_datetime'] ) <= time() && strtotime( $banner['end_datetime'] ) >= time() ) {
							if ( $most_recent_banner === null || strtotime( $banner['start_datetime'] ) > strtotime( $most_recent_banner['start_datetime'] ) ) {
								$most_recent_banner = $banner;
							}
						}
					}

					if ( $most_recent_banner ) {
						$start_datetime_formatted = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $most_recent_banner['start_datetime'] ) );
						$end_datetime_formatted   = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $most_recent_banner['end_datetime'] ) );

						echo '<div id="genex-alert-banner" class="genex-alert-banner">';
						echo '<p>' . wp_kses_post( $most_recent_banner['content'] ) . '</p>';
						echo '<p class="genex-alert-banner-date">Start Date: ' . esc_html( $start_datetime_formatted ) . ' - End Date: ' . esc_html( $end_datetime_formatted ) . '</p>';
						if ( count( $banners ) > 1 ) {
							echo '<a href="#">View all active messages</a>';
						}
						echo '</div>';
					}
				}
			}
		}
	}

	public function get_hub_active_banners_query(){
		$args = array(
			'post_type'      => 'alert_banner',
			'posts_per_page' => - 1, // Get all active banners
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
			'orderby'        => 'date',
			'order'          => 'DESC',
		);
		return new WP_Query( $args );
	}
	public function get_active_banners_callback() {
		$alert_type = get_option( 'genex_alert_type', 'hub' );
		$banners = array();
		if ( $alert_type === 'hub' ) {
			$query   = $this->get_hub_active_banners_query();
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
            // return $banners;
			wp_send_json_success($banners);
		}
		else{
			$plugin_client = new Genex_Client( $this->plugin_name, $this->version );
			$_banners       = $plugin_client->get_banners();
			if ( ! empty( $_banners ) ) {
				foreach ( $_banners as $banner ) {
					$banners[] = array(
                        'id'             => $banner['id'],
                        'title'          => $banner['title'],
                        'content'        => $banner['content'],
                        'start_datetime' => $banner['start_datetime'],
                        'end_datetime'   => $banner['end_datetime'],
                        'status'         => $banner['status'],
                    );
				}
				wp_send_json_success($banners);
			}
		}
		wp_die();

	}
}
