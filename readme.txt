=== Seraphinite Downloads Statistics ===
Contributors: seraphinitesoft
Donate link: https://www.s-sols.com/products/wordpress/downloads-stats#offer
Tags: download,directory,statistic,download monitor,analytics
Requires PHP: 5.4
Requires at least: 4.5
Tested up to: 6.0
Stable tag: 1.3.1
License: GPLv2 or later (if another license is not provided)
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Privacy policy: https://www.s-sols.com/privacy-policy

Measure direct downloads from your site.

== Description ==

Measure direct downloads from your site and publish it to Google Analytics. See more [how to use it](https://www.s-sols.com/docs/wordpress/downloads-stats/getting-started-dlstat).


**Features**

*	**Any relative path**
	Just [type a site's path](https://www.s-sols.com/docs/wordpress/downloads-stats/getting-started-dlstat#settings_item_path) to monitor.
*	**Google Analytics**
	Write statistics to [Google Analytics](https://www.s-sols.com/docs/wordpress/downloads-stats/getting-started-dlstat#settings_ga).
*	**Local Database**
	Write statistics to the [local database](https://www.s-sols.com/docs/wordpress/downloads-stats/getting-started-dlstat#settings_ldb).

**Premium features**

*	**Multiple paths**
	Using [multiple paths](https://www.s-sols.com/docs/wordpress/downloads-stats/getting-started-dlstat#settings_item_path) to increase flexibility and performance.
*	**No promotions**
	No promotions of other related plugins.
*	**Support**
	Personal prioritized [support](https://www.s-sols.com/support)

[More details](https://www.s-sols.com/products/wordpress/downloads-stats).

**Requirements**

*	[WordPress](https://wordpress.org/download) 4.5 or higher.
*	PHP 5.4 or higher.
*	[Apache Web Server](https://httpd.apache.org) 2.0 or higher (for site's .htaccess file).
*	Browser (Google Chrome, Firefox, IE).

== Installation ==

1. Choose the plugin from the WordPress repository, or choose the plugin's archive file in 'Upload Plugin' section in WordPress 'Plugins\Add New', or upload and extract the plugin archive to the '/wp-content/plugins' directory manually.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. It will appear in the admin UI as shown in the [screenshots](http://wordpress.org/plugins/seraphinite-downloads-stats/screenshots).

== Screenshots ==

1. Settings.

== Changelog ==

= 1.3.1 =

Improvements:

* Auto-renaming (while activation or upgrading from Base version) plugin's directory to appended by '-ext' to avoid external wrong overwriting by Base version.

Fixes:

* Files that contain spaces return 404.

= 1.3 =

Behavior changes:

* New file interception for speed and security.

Improvements:

* Changing text 'Already done' to 'Dismiss' in review notification.
* Direct link to download full version in upgrade message.
* If EULA is not accepted then showing minimal UI.
* Import/export of settings.
* Input-output security improvements.
* Last access time in 'Local Database'.
* Minimum WordPress version is 4.5.
* On all notifications that requires confirming the close 'X' button was removed.
* Options: Multisite support.
* Plugin's custom directory name support.
* Premium update.
* Settings restoring confirmation.
* Support for NGINX configuration files.
* The support button now opens the site page instead of the email client.
* Upgrading from free version to full.

Fixes:

* Admin scripts.
* Ajax requests could be blocked by another plugins.
* Can't update plugin from file if its directory is renamed.
* Cron: Maximum execution time of 30 seconds exceeded.
* Decrypting is not working after changing salts.
* Localization is not reloaded on 'change_locale' event.
* Mismatched version is always shown as new.
* Plugin's scripts and styles are loaded incorrectly if WP plugins directory is not under WP root directory.
* Sometimes Ext, Full versions are updated to Base version.
* Sometimes error appears about call to undefined function 'get_plugins'.
* The activation panel is not visible if the server is unavailable.
* Transferring files is always in binary mode.
* Unable to upgrade Extended and Premium version.
* Update terminates due to timeout on some hosting.
* Updating to full version is not always working.

= 1.2.1 =

Improvements:

* Russian localization correction.
* Upgrading to preview version trough downloading.

= 1.2 =

New features:

* Polylang plugin support.

Improvements:

* Backup previous settings structure.
* Making backups when change .htaccess.
* Not meeting minimum requirements notifications.
* Posting events after object sending without blocking request.
* Reset settings.
* Security: sanitizing input parameters.

Fixes:

* 'Key' buttons might have background on some themes.
* .htaccess rules might block other files redirections.
* Compatibility issues with Polylang plugin.
* Frontend plugin queries are not valid for some sites.
* License block is invisible just after installation if remote configuration is unavailable.
* Output on some sites might be broken.
* PHP 8: Fatal error on plugin initialization (call_user_func).
* PHP Compatibility Checker by WPEngine detects issues with PHP 7.3.
* Settings: 'Save changes' button is always in English.

= 1.1.3 =

Improvements:

* List items operations animation.
* Storing settings in JSON format to ensure import/export of data.

Fixes:

* Block's help button is shifted to right.
* Call to undefined function: wpml_element_type_filter.
* Multiple appearing of Change Version warning.
* Separator line is invisible under WordPress 5.2 or higher.

= 1.1.2 =

Improvements:

* Behavior changes notification warning.
* Checkboxes inner select links are now in Combo style.
* Download Preview and Full bundles by current version.

Fixes:

* "Key" link after "Order" button is invalid.
* In rare cases admin UI is blocked.
* In the admin panel, the warning 'Undefined index' is shown, if DEBUG mode is enabled.
* Inline comboboxes too short in WP 5.3 or higher.
* On some systems, script loading fails, resulting in a site loading error.
* PHP 5.4 'empty' operator compatibility.
* Settings layout is too wide on some themes.
* Unable to upgrade Extended and Premium version.

= 1.1.1 =

Fixes:

* Save settings result message is blocked by security plugins.
* Save settings result message is blocked by security plugins.
* While activation an alert is shown about unexpected output.

= 1.1 =

Improvements:

* Files extensions filter.
* Freemium mode.
* Help and documentation.
* Localization - Russian.
* Multiple paths.
* Unicode paths support.

Fixes:

* Sites with path are unsupported.

= 1.0.9 =

New features:

* Hooking files download in specified directory.

