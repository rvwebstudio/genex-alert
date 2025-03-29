<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://www.genexmarketing.com/
 * @since      1.0.0
 *
 * @package    Genex_Alert_Banner
 * @subpackage Genex_Alert_Banner/admin/partials
 */
?>

<div class="form-field">
	<label for="start_datetime"><?php _e( 'Start Date/Time', 'genex-alert-banner' ); ?></label>
	<input type="datetime-local" id="start_datetime" name="start_datetime" value="<?php echo esc_attr( $start_datetime ); ?>" required>
</div>

<div class="form-field">
	<label for="end_datetime"><?php _e( 'End Date/Time', 'genex-alert-banner' ); ?></label>
	<input type="datetime-local" id="end_datetime" name="end_datetime" value="<?php echo esc_attr( $end_datetime ); ?>" required>
</div>

<div class="form-field">
	<label for="status"><?php _e( 'Status', 'genex-alert-banner' ); ?></label>
	<select id="status" name="status">
		<option value="active" <?php selected( $status, 'active' ); ?>><?php _e( 'Active', 'genex-alert-banner' ); ?></option>
		<option value="inactive" <?php selected( $status, 'inactive' ); ?>><?php _e( 'Inactive', 'genex-alert-banner' ); ?></option>
	</select>
</div>
