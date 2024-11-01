<?php

/**
 * Fired during plugin activation
 *
 * @link       http://therevoltgroup.com
 * @since      1.0.0
 *
 * @package    Update_Alerts
 * @subpackage Update_Alerts/includes
 */

global $ua_db_version;
$ua_db_version = '1.0';

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Update_Alerts
 * @subpackage Update_Alerts/includes
 * @author     Andrew Karetas <andrew@therevoltgroup.com>
 */
class Update_Alerts_Activator {


	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
        // clear any existing schedules first
        wp_clear_scheduled_hook( 'update_alerts_refresh' );
        // schedule daily check
        wp_schedule_event( time(), 'daily', 'update_alerts_refresh' );

        // setup the database
        global $wpdb;
        global $ua_db_version;

        $table_name = $wpdb->prefix . 'update_alerts';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
                id mediumint(9) NOT NULL AUTO_INCREMENT,
                `plugin_name` text NOT NULL,
                `current_version` text NOT NULL,
                `updated_version` text NOT NULL,
                `date` datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                PRIMARY KEY  (id)
        )    $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        add_option( 'ua_db_version', $ua_db_version );
	}

}
