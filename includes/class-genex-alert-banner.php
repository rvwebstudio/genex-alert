<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @since      1.0.0
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/includes
 */
class Genex_Alert_Banner {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Genex_Alert_Banner_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'GENEX_ALERT_BANNER_VERSION' ) ) {
			$this->version = GENEX_ALERT_BANNER_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'genex-alert-banner';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Genex_Alert_Banner_Loader. Orchestrates the hooks of the plugin.
	 * - Genex_Alert_Banner_i18n. Defines internationalization functionality.
	 * - Genex_Alert_Banner_Admin. Defines all hooks for the admin area.
	 * - Genex_Alert_Banner_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'class-genex-alert-banner-loader.php';
		$this->loader = new Genex_Alert_Banner_Loader(); // Initialize the loader first!

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		//require_once plugin_dir_path( dirname( __FILE__ ) ) . 'class-genex-alert-banner-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'class-genex-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'class-genex-frontend.php';

		/**
		 * The class responsible for defining all actions that occur in the settings.
		 */
		require_once plugin_dir_path( __FILE__ ) . 'class-genex-settings.php';
		$plugin_settings = new Genex_Settings( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_menu', $plugin_settings, 'add_settings_page' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'register_settings' );

		$alert_type = get_option( 'genex_alert_type', 'hub' );
		if ( $alert_type === 'hub' ) {
			require_once plugin_dir_path( __FILE__ ) . 'class-genex-hub.php';
		} elseif ( $alert_type === 'client' ) {
			require_once plugin_dir_path( __FILE__ ) . 'class-genex-client.php';
		}

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Genex_Alert_Banner_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	// private function set_locale() {

	// 	$plugin_i18n = new Genex_Alert_Banner_i18n();

	// 	$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	// }

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {
		$is_first_run = ! get_option( 'genex_alert_type' );
		$plugin_admin = new Genex_Admin( $this->get_plugin_name(), $this->get_version(), $this->loader );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		$alert_type = get_option( 'genex_alert_type', 'hub' );
		if ( $alert_type === 'hub' && ! $is_first_run ) {
			$this->loader->add_action( 'init', $plugin_admin, 'register_cpt' );
			$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'add_meta_boxes' );
			$this->loader->add_action( 'save_post', $plugin_admin, 'save_meta_boxes' );

			$plugin_hub = new Genex_Hub( $this->get_plugin_name(), $this->get_version() );
			$this->loader->add_action( 'rest_api_init', $plugin_hub, 'register_api_endpoints' );
		} elseif ( $alert_type === 'client' && ! $is_first_run ) {
			$plugin_client = new Genex_Client( $this->get_plugin_name(), $this->get_version() );
		}

		$plugin_public = new Genex_Frontend( $this->get_plugin_name(), $this->get_version() );
        $this->loader->add_action('wp_ajax_get_active_banners', $plugin_public, 'get_active_banners_callback');
        // $this->loader->add_action('wp_ajax_nopriv_get_hub_active_banners', $plugin_public, 'get_hub_active_banners_callback');
        // $this->loader->add_action('wp_ajax_get_client_active_banners', $plugin_public, 'get_client_active_banners_callback');
        // $this->loader->add_action('wp_ajax_nopriv_get_client_active_banners', $plugin_public, 'get_client_active_banners_callback');
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Genex_Frontend( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_footer', $plugin_public, 'display_banner' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Genex_Alert_Banner_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Fired during plugin activation
	 */
	public static function activate() {
		$alert_type = get_option( 'genex_alert_type', 'hub' );
		if ( $alert_type === 'client' ) {
			Genex_Client::activate();
		}
	}

	/**
	 * Fired during plugin deactivation
	 */
	public static function deactivate() {
		$alert_type = get_option( 'genex_alert_type', 'hub' );
		if ( $alert_type === 'client' ) {
			Genex_Client::deactivate();
		}
	}

}
