<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.genexmarketing.com/
 * @since      1.0.0
 *
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/admin
 * @author     Ravi Bhatia <your.email@example.com>
 */
class Genex_Admin {

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
	 * The loader to add hooks.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      Genex_Alert_Banner_Loader
	 */
	private $loader;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 * @param      Genex_Alert_Banner_Loader $loader
	 */
	public function __construct( $plugin_name, $version, $loader ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->loader      = $loader;
		// $this->define_admin_hooks();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * which will then run the defined hooks with WordPress.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'admin/css/admin-styles.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * which will then run the defined hooks with WordPress.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( dirname( __FILE__ ) ) . 'admin/js/admin-scripts.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add meta boxes to the alert_banner custom post type.
	 *
	 * @since    1.0.0
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'alert_banner_meta',
			__( 'Alert Banner Details', 'genex-alert-banner' ),
			array( $this, 'render_meta_box' ),
			'alert_banner',
			'normal',
			'default'
		);
	}

	/**
	 * Render the meta box content.
	 *
	 * @since    1.0.0
	 * @param WP_Post $post The current post object.
	 */
	public function render_meta_box( $post ) {
		// Add nonce for security and authentication.
		wp_nonce_field( 'alert_banner_meta_action', 'alert_banner_meta_nonce' );

		// Retrieve an existing value from the database.
		$start_datetime = get_post_meta( $post->ID, 'start_datetime', true );
		$end_datetime   = get_post_meta( $post->ID, 'end_datetime', true );
		$status         = get_post_meta( $post->ID, 'status', true );

		// Set default values.
		if ( empty( $start_datetime ) ) {
			$start_datetime = date( 'Y-m-d H:i' );
		}
		if ( empty( $end_datetime ) ) {
			$end_datetime = date( 'Y-m-d H:i', strtotime( '+1 week' ) );
		}
		if ( empty( $status ) ) {
			$status = 'inactive';
		}

		// Form fields.
		include plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/meta-box-alert-banner.php';
	}

	/**
	 * Save the meta box data.
	 *
	 * @since    1.0.0
	 * @param int $post_id The ID of the post being saved.
	 */
	public function save_meta_boxes( $post_id ) {
		// Check if our nonce is set.
		if ( ! isset( $_POST['alert_banner_meta_nonce'] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST['alert_banner_meta_nonce'], 'alert_banner_meta_action' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check the user's permissions.
		if ( isset( $_POST['post_type'] ) && 'alert_banner' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
		}

		// Sanitize user input.
		$start_datetime = sanitize_text_field( $_POST['start_datetime'] );
		$end_datetime   = sanitize_text_field( $_POST['end_datetime'] );
		$status         = sanitize_text_field( $_POST['status'] );

		// Update the meta field in the database.
		update_post_meta( $post_id, 'start_datetime', $start_datetime );
		update_post_meta( $post_id, 'end_datetime', $end_datetime );
		update_post_meta( $post_id, 'status', $status );
	}

	/**
	 * Register the custom post type.
	 *
	 * @since    1.0.0
	 */
	public function register_cpt() {
		$labels = array(
			'name'                  => _x( 'Alert Banners', 'Post type general name', 'genex-alert-banner' ),
			'singular_name'         => _x( 'Alert Banner', 'Post type singular name', 'genex-alert-banner' ),
			'menu_name'             => _x( 'Alert Banners', 'Admin Menu text', 'genex-alert-banner' ),
			'name_admin_bar'        => _x( 'Alert Banner', 'Add New on Toolbar', 'genex-alert-banner' ),
			'add_new'               => __( 'Add New', 'genex-alert-banner' ),
			'add_new_item'          => __( 'Add New Alert Banner', 'genex-alert-banner' ),
			'new_item'              => __( 'New Alert Banner', 'genex-alert-banner' ),
			'edit_item'             => __( 'Edit Alert Banner', 'genex-alert-banner' ),
			'view_item'             => __( 'View Alert Banner', 'genex-alert-banner' ),
			'all_items'             => __( 'All Alert Banners', 'genex-alert-banner' ),
			'search_items'          => __( 'Search Alert Banners', 'genex-alert-banner' ),
			'parent_item_colon'     => __( 'Parent Alert Banners:', 'genex-alert-banner' ),
			'not_found'             => __( 'No alert banners found.', 'genex-alert-banner' ),
			'not_found_in_trash'    => __( 'No alert banners found in Trash.', 'genex-alert-banner' ),
			'featured_image'        => _x( 'Alert Banner Cover Image', 'Overrides the “Featured Image” phrase for this post type. Added in 4.3', 'genex-alert-banner' ),
			'set_featured_image'    => _x( 'Set cover image', 'Overrides the “Set featured image” phrase for this post type. Added in 4.3', 'genex-alert-banner' ),
			'remove_featured_image' => _x( 'Remove cover image', 'Overrides the “Remove featured image” phrase for this post type. Added in 4.3', 'genex-alert-banner' ),
			'use_featured_image'    => _x( 'Use as cover image', 'Overrides the “Use as featured image” phrase for this post type. Added in 4.3', 'genex-alert-banner' ),
			'archives'              => _x( 'Alert Banner archives', 'The post type archive label used in nav menus. Default “Post Archives”. Added in 4.4', 'genex-alert-banner' ),
			'insert_into_item'      => _x( 'Insert into alert banner', 'Overrides the “Insert into post”/”Insert into page” phrase (used when inserting media into a post). Added in 4.4', 'genex-alert-banner' ),
			'uploaded_to_this_item' => _x( 'Uploaded to this alert banner', 'Overrides the “Uploaded to this post”/”Uploaded to this page” phrase (used when viewing media attached to a post). Added in 4.4', 'genex-alert-banner' ),
			'filter_items_list'     => _x( 'Filter alert banners list', 'Screen reader text for the filter links heading on the post type listing screen. Default “Filter posts list”/”Filter pages list”. Added in 4.4', 'genex-alert-banner' ),
			'items_list_navigation' => _x( 'Alert Banners list navigation', 'Screen reader text for the pagination heading on the post type listing screen. Default “Posts list navigation”/”Pages list navigation”. Added in 4.4', 'genex-alert-banner' ),
			'items_list'            => _x( 'Alert Banners list', 'Screen reader text for the items list heading on the post type listing screen. Default “Posts list”/”Pages list”. Added in 4.4', 'genex-alert-banner' ),
		);

		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'alert-banner' ),
			'capability_type'    => 'post',
			'has_archive'        => false,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor' ),
			'menu_icon'          => 'dashicons-megaphone',
		);

		register_post_type( 'alert_banner', $args );
	}
}
