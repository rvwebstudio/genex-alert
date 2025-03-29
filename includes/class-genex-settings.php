<?php
/**
 * The settings functionality of the plugin.
 *
 * @link       https://www.genexmarketing.com/
 * @since      1.0.0
 *
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/includes
 */

/**
 * The settings functionality of the plugin.
 *
 * Defines the plugin name, version, and registers the settings page.
 *
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/includes
 * @author     Ravi Bhatia <your.email@example.com>
 */
class Genex_Settings {

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
	 * Add the settings page to the admin menu.
	 *
	 * @since    1.0.0
	 */
	public function add_settings_page() {
		add_options_page(
			__( 'Genex Alert Settings', 'genex-alert-banner' ), // Page title
			__( 'Genex Alert', 'genex-alert-banner' ), // Menu title
			'manage_options', // Capability
			'genex-alert-settings', // Menu slug
			array( $this, 'settings_page_html' ) // Callback function
		);
	}

	/**
	 * Register the settings.
	 *
	 * @since    1.0.0
	 */
	public function register_settings() {
		register_setting(
			'genex-alert-settings-group', // Option group
			'genex_alert_type', // Option name
			array( $this, 'sanitize_settings' ) // Sanitize callback
		);
		register_setting(
			'genex-alert-settings-group', // Option group
			'genex_hub_url', // Option name
			array( $this, 'sanitize_settings' ) // Sanitize callback
		);
		register_setting(
			'genex-alert-settings-group', // Option group
			'genex_hub_api_key', // Option name
			array( $this, 'sanitize_settings' ) // Sanitize callback
		);

		add_settings_section(
			'genex-alert-settings-section', // ID
			__( 'Genex Alert Settings', 'genex-alert-banner' ), // Title
			array( $this, 'settings_section_callback' ), // Callback
			'genex-alert-settings' // Page
		);

		add_settings_field(
			'genex_alert_type', // ID
			__( 'Site Type', 'genex-alert-banner' ), // Title
			array( $this, 'alert_type_callback' ), // Callback
			'genex-alert-settings', // Page
			'genex-alert-settings-section' // Section
		);
		add_settings_field(
			'genex_hub_url', // ID
			__( 'Hub URL', 'genex-alert-banner' ), // Title
			array( $this, 'hub_url_callback' ), // Callback
			'genex-alert-settings', // Page
			'genex-alert-settings-section', // Section
			array('class' => 'client-only-field')
		);
		add_settings_field(
			'genex_hub_api_key', // ID
			__( 'Hub API Key', 'genex-alert-banner' ), // Title
			array( $this, 'hub_api_key_callback' ), // Callback
			'genex-alert-settings', // Page
			'genex-alert-settings-section', // Section
			array('class' => 'client-only-field')
		);
	}

	/**
	 * Sanitize the settings.
	 *
	 * @since    1.0.0
	 * @param array $input The input settings.
	 * @return array The sanitized settings.
	 */
	public function sanitize_settings( $input ) {
		return sanitize_text_field( $input );
	}

	/**
	 * Settings section callback.
	 *
	 * @since    1.0.0
	 */
	public function settings_section_callback() {
		echo '<p>' . __( 'Configure your Genex Alert Banner settings.', 'genex-alert-banner' ) . '</p>';
	}

	/**
	 * Alert type callback.
	 *
	 * @since    1.0.0
	 */
	public function alert_type_callback() {
		$alert_type = get_option( 'genex_alert_type', 'hub' );
		?>
		<select id="genex_alert_type" name="genex_alert_type">
			<option value="hub" <?php selected( $alert_type, 'hub' ); ?>><?php _e( 'Hub Site', 'genex-alert-banner' ); ?></option>
			<option value="client" <?php selected( $alert_type, 'client' ); ?>><?php _e( 'Client Site', 'genex-alert-banner' ); ?></option>
		</select>
		<?php
	}

	/**
	 * Hub URL callback.
	 *
	 * @since    1.0.0
	 */
	public function hub_url_callback() {
		$hub_url    = get_option( 'genex_hub_url' );
		?>
		<div id="genex_hub_url_field">
			<input type="url" id="genex_hub_url" name="genex_hub_url" value="<?php echo esc_attr( $hub_url ); ?>" class="regular-text">
		</div>
		<?php
	}

	/**
	 * Hub API key callback.
	 *
	 * @since    1.0.0
	 */
	public function hub_api_key_callback() {
		$hub_api_key = get_option( 'genex_hub_api_key' );
		?>
		<div id="genex_hub_api_key_field">
			<input type="text" id="genex_hub_api_key" name="genex_hub_api_key" value="<?php echo esc_attr( $hub_api_key ); ?>" class="regular-text">
		</div>
		<?php
	}

	/**
	 * Settings page HTML.
	 *
	 * @since    1.0.0
	 */
	public function settings_page_html() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$alert_type = get_option( 'genex_alert_type', 'hub' );
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<form action="options.php" method="post">
				<?php
				settings_fields( 'genex-alert-settings-group' );
				do_settings_sections( 'genex-alert-settings' );
				submit_button( __( 'Save Settings', 'genex-alert-banner' ) );
				?>
			</form>
			<?php if ( $alert_type === 'hub' ) : ?>
				<a href="<?php echo esc_url( admin_url( 'edit.php?post_type=alert_banner' ) ); ?>" class="button add-edit-alert-banners"><?php _e( 'Add/Edit Alert Banners', 'genex-alert-banner' ); ?></a>
			<?php endif; ?>
		</div>
		<script>
			jQuery(document).ready(function($) {
				function toggleHubFields() {
					var alertType = $('#genex_alert_type').val();
					if (alertType === 'hub') {
						$('.client-only-field').hide()
					} else {
						$('.client-only-field').show()
					}
				}

				// Initial state
				toggleHubFields();

				// On change
				$('#genex_alert_type').change(function() {
					toggleHubFields();
				});
			});
		</script>
		<?php
	}
}
