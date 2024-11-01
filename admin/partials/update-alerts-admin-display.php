<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://therevoltgroup.com
 * @since      1.0.0
 *
 * @package    Update_Alerts
 * @subpackage Update_Alerts/admin/partials
 */
?>
<div class="wrap">
    <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
    <form <?php if ( is_multisite() ) {
		echo 'action="edit.php?action=Update_UA_Options"';
	}; ?> method="post">
		<?php
		settings_fields( $this->plugin_name );
		do_settings_sections( $this->plugin_name );
		submit_button();
		wp_nonce_field( 'Update_UA_Options', 'update_alerts_nonce' );
		?>
    </form>


    <!-- This file should primarily consist of HTML with a little bit of PHP. -->
