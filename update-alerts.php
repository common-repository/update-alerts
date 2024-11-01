<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://therevoltgroup.com
 * @since             1.0.0
 * @package           Update_Alerts
 *
 * @wordpress-plugin
 * Plugin Name:       Update Alerts
 * Plugin URI:        http://wordpress.org/plugins/update-alerts
 * Description:       Alert admins to plugin and WordPress core updates
 * Version:           1.3.1
 * Author:            The Revolt Group
 * Author URI:        http://therevoltgroup.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       update-alerts
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'UPDATE_ALERTS_VERSION', '1.3.1 ' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-update-alerts-activator.php
 */
function activate_update_alerts() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-update-alerts-activator.php';
	Update_Alerts_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-update-alerts-deactivator.php
 */
function deactivate_update_alerts() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-update-alerts-deactivator.php';
	Update_Alerts_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_update_alerts' );
register_deactivation_hook( __FILE__, 'deactivate_update_alerts' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-update-alerts.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_update_alerts() {

	$plugin = new Update_Alerts();
	$plugin->run();

}

run_update_alerts();
