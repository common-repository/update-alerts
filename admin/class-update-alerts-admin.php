<?php
require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/objects/Plugin.php';
if ( ! class_exists( 'WP_Http' ) ) {
	include_once( ABSPATH . WPINC . '/class-http.php' );
}

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://therevoltgroup.com
 * @since      1.0.0
 *
 * @package    Update_Alerts
 * @subpackage Update_Alerts/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Update_Alerts
 * @subpackage Update_Alerts/admin
 * @author     Andrew Karetas <andrew@therevoltgroup.com>
 */
class Update_Alerts_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * The options name to be used in this plugin
	 *
	 * @since    1.0.0
	 * @access    private
	 * @var    string $option_name Option name of this plugin
	 */
	private $option_name = 'update_alerts';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

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
		 * defined in Update_Alerts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Update_Alerts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/update-alerts-admin.css', array(), $this->version, 'all' );

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
		 * defined in Update_Alerts_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Update_Alerts_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/update-alerts-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Add an options page under the Settings submenu
	 *
	 * @since  1.0.0
	 */
	public function add_ua_options_page() {
		if ( is_multisite() ) {
			$this->plugin_screen_hook_suffix = add_submenu_page(
				'settings.php',
				__( 'Update Alerts Settings', 'update-alerts' ),
				__( 'Update Alerts', 'update-alerts' ),
				'manage_network_options',
				$this->plugin_name,
				array( $this, 'display_options_page' )
			);
		} else {
			add_options_page(
				__( 'Update Alerts', 'update-alerts' ),
				__( 'Update Alerts', 'update-alerts' ),
				'manage_options',
				$this->plugin_name,
				array( $this, 'display_options_page' )
			);
		}
	}

	/**
	 * Render the options page for plugin
	 *
	 * @since  1.0.0
	 */
	public function display_options_page() {

		include_once 'partials/update-alerts-admin-display.php';
		$this->update_ua_options();
	}

	/**
	 * Add http authentication to wp-cron requests
	 *
	 * @since 1.3.0
	 */
	public function wp_cron_http_auth( $cron_request ) {
		$auth_username = get_site_option( $this->option_name . '_authUsername' );
		$auth_password = get_site_option( $this->option_name . '_authPassword' );
		if ( $auth_username && $auth_password ) {
			$headers = array( 'Authorization' => sprintf( 'Basic %s', base64_encode( $auth_username . ':' . htmlspecialchars_decode( $auth_password ) ) ) );

			$cron_request['args']['headers'] = isset( $cron_request['args']['headers'] ) ? array_merge( $cron_request['args']['headers'], $headers ) : $headers;

		}

		return $cron_request;
	}

	/**
	 * Register the settings section for the plugin
	 *
	 * @since 1.0.0
	 */
	public function register_setting() {
		// Add a General section
		add_settings_section(
			$this->option_name . '_general',
			__( 'General', 'update-alerts' ),
			array( $this, $this->option_name . '_general_cb' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_projectSlug',
			__( 'Site name identifier', 'update-alerts' ),
			array( $this, $this->option_name . '_projectSlug_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_projectSlug' )
		);

		add_settings_field(
			$this->option_name . '_issueType',
			__( 'Jira issue type id', 'update-alerts' ),
			array( $this, $this->option_name . '_issueType_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_issueType' )
		);

		add_settings_field(
			$this->option_name . '_day',
			__( 'Add reminder to ticket after', 'update-alerts' ),
			array( $this, $this->option_name . '_day_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_day' )
		);

		add_settings_field(
			$this->option_name . '_authUsername',
			__( 'Http authentication username', 'update-alerts' ),
			array( $this, $this->option_name . '_authUsername_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_authUsername' )
		);

		add_settings_field(
			$this->option_name . '_authPassword',
			__( 'Http authentication password', 'update-alerts' ),
			array( $this, $this->option_name . '_authPassword_cb' ),
			$this->plugin_name,
			$this->option_name . '_general',
			array( 'label_for' => $this->option_name . '_authPassword' )
		);


		// Add an Email Alert section
		add_settings_section(
			$this->option_name . '_email',
			__( 'Email Alerts', 'update-alerts' ),
			array( $this, $this->option_name . '_email_cb' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_emailTo',
			__( 'Where to send alert', 'update-alerts' ),
			array( $this, $this->option_name . '_emailTo_cb' ),
			$this->plugin_name,
			$this->option_name . '_email',
			array( 'label_for' => $this->option_name . '_emailTo' )
		);

		add_settings_field(
			$this->option_name . '_secondEmail',
			__( 'Second email to send alert', 'update-alerts' ),
			array( $this, $this->option_name . '_secondEmail_cb' ),
			$this->plugin_name,
			$this->option_name . '_email',
			array( 'label_for' => $this->option_name . '_secondEmail' )
		);

		// Add an Microsoft Flow section
		add_settings_section(
			$this->option_name . '_web',
			__( 'Webhook Integrations', 'update-alerts' ),
			array( $this, $this->option_name . '_web_cb' ),
			$this->plugin_name
		);

		add_settings_field(
			$this->option_name . '_flowEndpoint',
			__( 'Microsoft Flow Alerts', 'update-alerts' ),
			array( $this, $this->option_name . '_flowEndpoint_cb' ),
			$this->plugin_name,
			$this->option_name . '_web',
			array( 'label_for' => $this->option_name . '_flowEndpoint' )
		);

		add_settings_field(
			$this->option_name . '_iftttEndpoint',
			__( 'IFTTT Webhook Alerts', 'update-alerts' ),
			array( $this, $this->option_name . '_iftttEndpoint_cb' ),
			$this->plugin_name,
			$this->option_name . '_web',
			array( 'label_for' => $this->option_name . '_iftttEndpoint' )
		);

		add_settings_field(
			$this->option_name . '_zapEndpoint',
			__( 'Zapier Webhook Alerts', 'update-alerts' ),
			array( $this, $this->option_name . '_zapEndpoint_cb' ),
			$this->plugin_name,
			$this->option_name . '_web',
			array( 'label_for' => $this->option_name . '_zapEndpoint' )
		);
	}

	public function update_ua_options() {
		if ( ! empty( $_POST ) && check_admin_referer( 'Update_UA_Options', 'update_alerts_nonce' ) ) {
			if ( is_multisite() ) {
				if ( ! current_user_can( 'manage_network_options' ) ) {
					wp_die( 'FU' );
				}
			} else {
				if ( ! current_user_can( 'manage_options' ) ) {
					wp_die( 'FU' );
				}
			}

			update_site_option( $this->option_name . '_projectSlug', sanitize_text_field( $_POST[ $this->option_name . '_projectSlug' ] ) );
			update_site_option( $this->option_name . '_issueType', intval( $_POST[ $this->option_name . '_issueType' ] ) );
			update_site_option( $this->option_name . '_day', intval( $_POST[ $this->option_name . '_day' ] ) );
			update_site_option( $this->option_name . '_authUsername', sanitize_text_field( $_POST[ $this->option_name . '_authUsername' ] ) );
			update_site_option( $this->option_name . '_authPassword', esc_attr( $_POST[ $this->option_name . '_authPassword' ] ) );
			update_site_option( $this->option_name . '_emailTo', $this->update_alerts_sanitize_email( $_POST[ $this->option_name . '_emailTo' ] ) );
			update_site_option( $this->option_name . '_secondEmail', $this->update_alerts_sanitize_email( $_POST[ $this->option_name . '_secondEmail' ] ) );
			update_site_option( $this->option_name . '_flowEndpoint', esc_url_raw( $_POST[ $this->option_name . '_flowEndpoint' ], array(
				"http",
				"https"
			) ) );
			update_site_option( $this->option_name . '_iftttEndpoint', esc_url_raw( $_POST[ $this->option_name . '_iftttEndpoint' ], array(
				"http",
				"https"
			) ) );
			update_site_option( $this->option_name . '_zapEndpoint', esc_url_raw( $_POST[ $this->option_name . '_zapEndpoint' ], array(
				"http",
				"https"
			) ) );

			if ( ! is_multisite() ) {
				wp_redirect( add_query_arg( array(
					'page'    => 'update-alerts',
					'updated' => 'true'
				), admin_url( 'options-general.php' ) ) );
			}
			if ( is_multisite() ) {
				wp_redirect( add_query_arg( array(
					'page'    => 'update-alerts',
					'updated' => 'true'
				), network_admin_url( 'settings.php' ) ) );
			}
		}

		exit();
	}

	/**
	 * Render the text for the general section
	 *
	 * @since  1.0.0
	 */
	public function update_alerts_general_cb() {

		echo '<p>' . __( 'Project slug will be used as a general identifier. If your endpoint is Jira ticket creation this value will need to be the exact project key string.', 'update-alerts' ) . '</p>';
		echo '<p>' . __( 'Issue type is optional. However if Jira is your endpoint this value will need to be an Issue Id that exists in your Jira project.' ) . '</p>';
		echo '<p>' . __( 'Http Authentication breaks wp-cron jobs. If you use authentication enter your credentials below to allow wp-cron to function properly. This is not intended for production use as the storage of this password is not encrypted.' ) . '</p>';
	}

	/**
	 * Render the text input for project slug to option
	 *
	 * @since  1.0.0
	 */
	public function update_alerts_projectSlug_cb() {
		$projectSlug = get_site_option( $this->option_name . '_projectSlug' );
		echo '<input type="text" name="' . $this->option_name . '_projectSlug' . '" id="' . $this->option_name . '_projectSlug' . '" value="' . $projectSlug . '">';
	}

	/**
	 * Render the text input for issue type to option
	 *
	 * @since  1.1.0
	 */
	public function update_alerts_issueType_cb() {
		$issueType = get_site_option( $this->option_name . '_issueType' );
		echo '<input type="text" name="' . $this->option_name . '_issueType' . '" id="' . $this->option_name . '_issueType' . '" value="' . $issueType . '">';
	}

	/**
	 * Render the text input for http authentication username option
	 *
	 * @since  1.3.0
	 */
	public function update_alerts_authUsername_cb() {
		$user = get_site_option( $this->option_name . '_authUsername' );
		echo '<input type="text" name="' . $this->option_name . '_authUsername' . '" id="' . $this->option_name . '_authUsername' . '" value="' . $user . '">';
	}

	/**
	 * Render the text input for http authentication username option
	 *
	 * @since  1.3.0
	 */
	public function update_alerts_authPassword_cb() {
		$pass = get_site_option( $this->option_name . '_authPassword' );
		echo '<input type="password" name="' . $this->option_name . '_authPassword' . '" id="' . $this->option_name . '_authPassword' . '" value="' . $pass . '">';
	}

	/**
	 * Render the text for the email alert section
	 *
	 * @since  1.1.0
	 */
	public function update_alerts_email_cb() {

		echo '<p>' . __( 'Provide up to two email addresses to be used for email alerts.', 'update-alerts' ) . '</p>';
		echo '<p>' . __( 'If left blank email alerts will not be sent.', 'update-alerts' ) . '</p>';
	}

	/**
	 * Render the text input for email to option
	 *
	 * @since  1.0.0
	 */
	public function update_alerts_emailTo_cb() {
		$emailTo = get_site_option( $this->option_name . '_emailTo' );
		echo '<input type="text" name="' . $this->option_name . '_emailTo' . '" id="' . $this->option_name . '_emailTo' . '" value="' . $emailTo . '">';
	}

	/**
	 * Render the text input for second email to option
	 *
	 * @since  1.0.0
	 */
	public function update_alerts_secondEmail_cb() {
		$secondEmail = get_site_option( $this->option_name . '_secondEmail' );
		echo '<input type="text" name="' . $this->option_name . '_secondEmail' . '" id="' . $this->option_name . '_secondEmail' . '" value="' . $secondEmail . '">';
	}

	/**
	 * Render the text for the webhook alert section
	 *
	 * @since  1.2.1
	 */
	public function update_alerts_web_cb() {

		echo '<p>' . __( 'Provide endpoint address of configured Webhook integration.', 'update-alerts' ) . '</p>';
		echo '<p>' . __( 'Plugin will send a JSON body containing: projectslug, issueType, summary, and description', 'update-alerts' ) . '</p>';
		echo '<p>' . __( '* issueType is only sent during Microsoft Flow webhook', 'update-alerts' ) . '</p>';
		echo '<p>' . __( 'If left blank the specific webhook alert will not be sent.', 'update-alerts' ) . '</p>';
	}


	/**
	 * Render the text input for Microsoft flow option
	 *
	 * @since  1.1.0
	 */
	public function update_alerts_flowEndpoint_cb() {
		$endpoint = get_site_option( $this->option_name . '_flowEndpoint' );
		echo '<input class="regular-text" type="text" name="' . $this->option_name . '_flowEndpoint' . '" id="' . $this->option_name . '_flowEndpoint' . '" value="' . $endpoint . '">';
	}

	/**
	 * Render the text input for IFTTT Webhook option
	 *
	 * @since  1.2.0
	 */
	public function update_alerts_iftttEndpoint_cb() {
		$endpoint = get_site_option( $this->option_name . '_iftttEndpoint' );
		echo '<input class="regular-text" type="text" name="' . $this->option_name . '_iftttEndpoint' . '" id="' . $this->option_name . '_iftttEndpoint' . '" value="' . $endpoint . '">';
	}

	/**
	 * Render the text input for Zapier Webhook option
	 *
	 * @since  1.2.0
	 */
	public function update_alerts_zapEndpoint_cb() {
		$endpoint = get_site_option( $this->option_name . '_zapEndpoint' );
		echo '<input class="regular-text" type="text" name="' . $this->option_name . '_zapEndpoint' . '" id="' . $this->option_name . '_zapEndpoint' . '" value="' . $endpoint . '">';
	}

	/**
	 * Render the treshold day input for this plugin
	 *
	 * @since  1.0.0
	 */
	public function update_alerts_day_cb() {
		$day = get_site_option( $this->option_name . '_day' );
		echo '<input type="text" name="' . $this->option_name . '_day' . '" id="' . $this->option_name . '_day' . '" value="' . $day . '"> ' . __( 'days', 'update-alerts' );
		echo '<br/><span style="font-size: 8pt">*If 0 days, reminder alerts will not be sent.</span>';
	}

	/**
	 * Sanitize the email address before being saved to database
	 *
	 * @param  string $input $_POST value
	 *
	 * @since  1.0.0
	 * @return string           Sanitized value
	 */
	public function update_alerts_sanitize_email( $input ) {
		$validated = sanitize_email( $input );

		return $validated;
	}

	/**
	 * Called daily to check plugin status
	 *
	 * @since 1.0.0
	 *
	 */
	public function update_alerts_updatechecker() {
		global $wpdb;
		global $wp_version;
		$cur_wp_version = preg_replace( '/-.*$/', '', $wp_version );
		//Check for Wordpress Core Updates
		$update_core = get_site_transient( 'update_core' );
		if ( ! empty( $update_core ) && isset( $update_core->updates ) && is_array( $update_core->updates )
		     && isset( $update_core->updates[0]->response ) && 'upgrade' == $update_core->updates[0]->response ) {
			$newversion  = $update_core->updates[0]->current;
			$oldversion  = $update_core->version_checked;
			$blogurl     = esc_url( home_url() );
			$summary     = 'Wordpress Core update available';
			$description = "It's time to update the version of WordPress running at $blogurl from version $oldversion to $newversion";
			// don't let $wp_version mangling plugins mess this up
			if ( ! preg_match( '/^(\d+\.)?(\d+\.)?(\d+)$/', $oldversion ) ) {
				include( ABSPATH . WPINC . '/version.php' );
				$description = $wp_version == $newversion ? '' : "It's time to update the version of WordPress running at $blogurl from version $oldversion to $newversion";
			}
			$data   = array(
				'plugin_name'     => 'wordpress-core',
				'current_version' => $oldversion,
				'updated_version' => $newversion,
				'date'            => date( "Y-m-d H:i:s" )
			);
			$result = $this->getEntry( 'wordpress-core' );
			$this->sendAlert( $result, $data, $summary, $description );
		}
		//Check for Plugin Updates
		$update_plugins = get_site_transient( 'update_plugins' );
		if ( ! empty( $update_plugins->response ) ) {
			//
			$plugins_needupdate = $update_plugins->response;
			foreach ( $plugins_needupdate as $key => $plugin ) {
				require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' ); // Required for plugin API
				require_once( ABSPATH . WPINC . '/version.php' ); // Required for WP core version
				$plugin_info = get_plugin_data( WP_PLUGIN_DIR . "/" . $key ); // get local plugin info
				$info        = plugins_api( 'plugin_information', array( 'slug' => $plugin->slug ) ); // get repository plugin info

				if ( isset( $info->tested ) && version_compare( $info->tested, $wp_version, '>=' ) ) {
					$compat = sprintf( __( 'Compatibility with WordPress %1$s: 100%% (according to its author)' ), $cur_wp_version );
				} elseif ( isset( $info->compatibility[ $wp_version ][ $data->new_version ] ) ) {
					$compat = $info->compatibility[ $wp_version ][ $data->new_version ];
					$compat = sprintf( __( 'Compatibility with WordPress %1$s: %2$d%% (%3$d "works" votes out of %4$d total)' ), $wp_version, $compat[0], $compat[2], $compat[1] );
				} else {
					$compat = sprintf( __( 'Compatibility with WordPress %1$s: Unknown' ), $wp_version );
				}

				//get stored result for specific plugin
				//info included: plugin name, current installed version, available update version, date
				$result      = $this->getEntry( $plugin_info['Name'] );
				$summary     = 'Plugin update available for ' . $plugin_info['Name'];
				$description = $plugin_info['Name'] . ' requires an update. the current installed version is ' . $plugin_info['Version'] . ', the new version is ' . $plugin->new_version . '. ' . $compat . ' You can find more about this update at ' . $plugin->url;
				$data        = array(
					'plugin_name'     => $plugin_info['Name'],
					'current_version' => $plugin_info['Version'],
					'updated_version' => $plugin->new_version,
					'date'            => date( "Y-m-d H:i:s" )
				);
				$this->sendAlert( $result, $data, $summary, $description );
			}
		}
		//Check for Theme Updates
		$update_themes = get_site_transient( 'update_themes' );
		if ( ! empty( $update_themes->response ) ) {
			$themes_needupdate = $update_themes->response;
			$active_themes     = array();
			if ( is_multisite() ) {
				$sites = wp_get_sites();
				foreach ( $sites as $site ) {
					switch_to_blog( $site['blog_id'] );
					$active_themes[ strtolower( get_option( 'current_theme' ) ) ] = "update";
					//array_push($active_themes, strtolower(get_option('current_theme')));
					restore_current_blog();
				}
			} else {
				$active_themes[ strtolower( get_option( 'current_theme' ) ) ] = "update";
				//array_push($active_themes, strtolower(get_option('current_theme')));
			}
			foreach ( $themes_needupdate as $update_theme ) {
				//loop through each active theme (may be multiple if on multisite)
				//compare against themes that need updates
				foreach ( $active_themes as $theme => $value ) {
					$theme_name = $update_theme['theme'];
					if ( strpos( $theme, $theme_name ) !== false ) {
						//active theme needs an update. Run check if alert is needed
						$result      = $this->getEntry( $theme_name );
						$summary     = 'Theme update available for ' . $theme_name . '<br />';
						$description = $theme . ' requires an update. The new version is ' . $update_theme['new_version'] . '. You can find more about this update at ' . $update_theme['url'] . '<br />';
						$data        = array(
							'plugin_name'     => $theme_name,
							'current_version' => $update_themes->checked[ $theme_name ],
							'updated_version' => $update_theme['new_version'],
							'date'            => date( "Y-m-d H:i:s" )
						);
						$this->sendAlert( $result, $data, $summary, $description );
					}
				}

			}
		}

	}

	private function sendAlert( $result, $data, $summary, $description ) {
		global $wpdb;
		$flowEndpoint  = get_site_option( $this->option_name . '_flowEndpoint' );
		$iftttEndpoint = get_site_option( $this->option_name . '_iftttEndpoint' );
		$zapEndpoint   = get_site_option( $this->option_name . '_zapEndpoint' );
		$issueType     = get_site_option( $this->option_name . '_issueType' );
		$project       = get_site_option( $this->option_name . '_projectSlug' );
		$emailTo       = get_site_option( $this->option_name . '_emailTo' );
		$secondEmail   = get_site_option( $this->option_name . '_secondEmail' );
		if ( ! empty( $result ) ) {
			if ( $data['current_version'] != '' && $data['current_version'] != $result['current_version'] ) {
				$updateDB = false;
				//plugin was updated since last check, but new version is now available

				//send new alert
				if ( $emailTo != '' ) {
					wp_mail( $emailTo, 'Plugin Update: ' . $project, 'Summary: ' . $summary . '<br />Description: ' . $description );
					$updateDB = true;
				}
				if ( $secondEmail != '' ) {
					wp_mail( $secondEmail, 'Plugin Update: ' . $project, 'Summary: ' . $summary . '<br />Description: ' . $description );
					$updateDB = true;
				}
				//if Microsoft Flow endpoint was supplied send Flow alert
				if ( $flowEndpoint != '' ) {
					$this->sendFlow( $flowEndpoint, $project, $issueType, $summary, $description );
					$updateDB = true;
				}
				//if IFTTT endpoint was supplied send IFTTT alert
				if ( $iftttEndpoint != '' ) {
					$this->sendIFTTT( $iftttEndpoint, $project, $issueType, $summary, $description );
					$updateDB = true;
				}
				//if Zapier endpoint was supplied send Zapier alert
				if ( $zapEndpoint != '' ) {
					$this->sendZapier( $zapEndpoint, $project, $issueType, $summary, $description );
					$updateDB = true;
				}
				//Update datebase if at least one setting is set
				if ( $updateDB === true ) {
					$wpdb->update( $wpdb->prefix . 'update_alerts', $data, array( 'plugin_name' => $data['plugin_name'] ) );
				}

			} elseif ( $data['updated_version'] != $result['updated_version'] ) {
				//plugin hasn't been updated since last check but the available updated version of the plugin has increased
				//don't send alert
				$updateDB = false;
				//todo: update jira ticket to include new updated version
				if ( $emailTo != '' ) {
					wp_mail( $emailTo, 'Plugin Reminder: ' . $project, $data['plugin_name'] . " hasn't been updated and the updated version has increased to " . $data['updated_version'] );
					$updateDB = true;
				}
				if ( $secondEmail != '' ) {
					wp_mail( $secondEmail, 'Plugin Reminder: ' . $project, $data['plugin_name'] . " hasn't been updated and the updated version has increased to " . $data['updated_version'] );
					$updateDB = true;
				}
				//Update datebase if at least one setting is set
				if ( $updateDB === true ) {
					$wpdb->update( $wpdb->prefix . 'update_alerts', $data, array( 'plugin_name' => $data['plugin_name'] ) );
				}
			} else {
				//Alert was already sent
				//don't send alert
				//todo:may want to append a bump to ticket depending on lapsed time
				//PC::debug("No new updates available");
			}
		} else {
			//There is a new plugin update
			$updateDB = false;
			//if email addresses exist send email alert
			if ( $emailTo != '' ) {
				wp_mail( $emailTo, 'Plugin Update: ' . $project, 'Summary: ' . $summary . '<br />Description: ' . $description );
				$updateDB = true;
			}
			if ( $secondEmail != '' ) {
				wp_mail( $secondEmail, 'Plugin Update: ' . $project, 'Summary: ' . $summary . '<br />Description: ' . $description );
				$updateDB = true;
			}
			//if Microsoft Flow endpoint was supplied send Flow alert
			if ( $flowEndpoint != '' ) {
				$this->sendFlow( $flowEndpoint, $project, $issueType, $summary, $description );
				$updateDB = true;
			}
			//if IFTTT endpoint was supplied send IFTTT alert
			if ( $iftttEndpoint != '' ) {
				$this->sendIFTTT( $iftttEndpoint, $project, $issueType, $summary, $description );
				$updateDB = true;
			}
			//if Zapier endpoint was supplied send Zapier alert
			if ( $zapEndpoint != '' ) {
				$this->sendZapier( $zapEndpoint, $project, $issueType, $summary, $description );
				$updateDB = true;
			}
			//Insert entry into datebase if at least one setting is set
			if ( $updateDB === true ) {
				$wpdb->insert( $wpdb->prefix . 'update_alerts', $data );
			}
		}
	}

	private function sendFlow( $url, $project, $issueType, $summary, $description ) {

		$headers  = array(
			'Content-Type'  => 'application/json',
			'Cache-Control' => 'no-cache'
		);
		$args     = array(
			'body'     => "{\"projectslug\": \"$project\", \"issuetype\": \"$issueType\", \"summary\": \"$summary\",\"description\": \"$description\"}",
			'timeout'  => '30',
			'headers'  => $headers,
			'blocking' => false
		);
		$response = wp_remote_post( esc_url_raw( $url ), $args );
	}

	private function sendIFTTT( $url, $project, $issueType, $summary, $description ) {

		$headers  = array(
			'Content-Type'  => 'application/json',
			'Cache-Control' => 'no-cache'
		);
		$args     = array(
			'body'     => "{\"value1\": \"$project\", \"value2\": \"$summary\", \"value3\": \"$description\"}",
			'timeout'  => '30',
			'headers'  => $headers,
			'blocking' => false
		);
		$response = wp_remote_post( esc_url_raw( $url ), $args );
	}

	private function sendZapier( $url, $project, $issueType, $summary, $description ) {

		$headers  = array(
			'Content-Type'  => 'application/json',
			'Cache-Control' => 'no-cache'
		);
		$args     = array(
			'body'     => "{\"Project\": \"$project\", \"Summary\": \"$summary\", \"Description\": \"$description\"}",
			'timeout'  => '30',
			'headers'  => $headers,
			'blocking' => false
		);
		$response = wp_remote_post( esc_url_raw( $url ), $args );
	}

	private function getEntry( $plugin_name ) {
		global $wpdb;
		$query = "SELECT * FROM {$wpdb->prefix}update_alerts WHERE plugin_name = '$plugin_name'";

		$results = $wpdb->get_results( $query, ARRAY_A );
		if ( ! empty( $results ) ) {
			return $results[0];
		}

		return null;
	}


}
