<?php

/**
 * Fired during plugin uninstallation
 *
 * @link       http://therevoltgroup.com
 * @since      1.0.0
 *
 * @package    Update_Alerts
 * @subpackage Update_Alerts/includes
 */


/**
 * Fired during plugin uninstallation.
 *
 * This class defines all code necessary to run during the plugin's uninstallation.
 *
 * @since      1.0.0
 * @package    Update_Alerts
 * @subpackage Update_Alerts/includes
 * @author     Andrew Karetas <andrew@therevoltgroup.com>
 */
class Update_Alerts_Uninstall
{


    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */
    public static function uninstall()
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'updatealerts';
        $sql = "DROP TABLE IF EXISTS $table_name";
    }
}