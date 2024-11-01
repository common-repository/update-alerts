=== Update Alerts ===
Contributors: porticue
Author URI: http://therevoltgroup.com
Author: The Revolt Group
Donate link: https://paypal.me/porticue
Tags: plugin_management, plugin_alerts
Requires at least: 4.6
Tested up to: 4.9.8
Stable tag: trunk
Requires PHP: 5.6
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Alert admins to plugin, active theme and WordPress core updates

== Description ==

Update Alerts allows you to configure various methods of receiving Core, Plugin, and Theme update notifications. It is very important to stay on top of updates for various security and stability reasons. With Update Alerts you can automate the process of starting and tracking updates. Simply be alerted via email, or create a complex flow that will start the ticketing process to keep track of updates and their progress.

  - Microsoft Flow can directly create an Atlassian Jira Ticket
  - IFTTT and Zapier can create Trello task cards
  - Create complex flows with any service to assist you in your wordpress environment health
  - Update Alerts works in either single site or multi-site implementations.

== Installation ==

Activate plugin. Under the settings tab configure plugin how to operate. This could be providing emails, webhook enpoints, or both.

== Frequently Asked Questions ==

= How will I be alerted? =

You can configure emails to be sent out to specified addresses. Create Microsoft Flow, IFTTT, Zapier Webhooks that will kick off your predefined actions.

== Screenshots ==

1. Create a Flow with an HTTP Request starting point. After it's configured you will copy the endpoint and paste under Flow Alerts.

2. View Services in IFTTT, find Webhooks and click documentation. This screen will provide the endpoint that you need for IFTTT Alerts. Be sure to change event_type before copying.

3. Find your Zap on your Zapier dashboard. Under the Catch Webhook trigger you will find View Webhook. Copy the endpoint and paste under Zapier Alerts.

== Changelog ==

= 1.3.1 =
* Won't update Database unless at least one setting is set. This prevents the case where activating plugin for the first time won't send out current update alerts
* Make sure to fully clean up on plugin deletion.

= 1.3.0 =
* Add basic HTTP Authentication credential settings.

= 1.2.1 =
* Settings page cleanup
* Setup screenshots for various integrations

= 1.2.0 =
* Added IFTTT support
* Added Zapier support
* Bug Fixes

= 1.1.0 =
* Added Microsoft Flow support

= 1.0 =
* Initial version


== Upgrade Notice ==

= 1.3.1 =
Previously if you activated the plugin for the first time and had existing updates, those updates would not be alerted because settings haven't had a chance to be set. Update Alerts now waits for at least one setting to be set before marking an alert sent.

= 1.3.0 =
Update Alerts uses wp-cron to automatically check if there are updates. wp-cron breaks when used under HTTP Authentication. You can now provide basic HTTP Authentication credentials so that wp-cron will continue to work. This is not recommended for production use as the password is not stored encrypted.

= 1.2.0 =
New sections in settings to send alerts out using IFTTT and Zapier webhooks. Use respective services to get proper endpoint URLs.

= 1.1.0 =
This update allows the direct calling of Microsoft Flow. Using an Http Request action as the entry point into the Flow will provide a request address string. Supply that address string in the plugin settings.

= 1.0 =
This is the initial release