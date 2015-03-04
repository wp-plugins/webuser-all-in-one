=== Plugin Name ===
Contributors: sem-wong
Donate link: 
Tags: webuser
Requires at least: 3.0.1
Tested up to: 4.1.1
Stable tag: /1.2.3/
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin developed by Webuser B.V. for Webuser customers.

== Description ==

Webuser All-in-One plugin adds functionality offered by Webuser. It allows Admin accounts to modify specific rights per user, it adds the Google Login API
and adds the Custom Header Module developed by Webuser.

* Webuser User Rights Module: Allows the Admin account to modify specific rights that each user possesses.
* Google Login API: Add Google Login to your website, allowing users to log into the Admin Panel through Google Login.
* Webuser Custom Headers: Allows users to add Custom Header Images in a set container on a specific page. This uses the Next-Gen Gallery plugin

== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Place `<?php do_action('plugin_name_hook'); ?>` in your templates

1. Make sure NextGen-Gallery plugin is installed
2. Upload the full content of Webuser-all-in-one.zip into '/wp-content/plugins/' directory
3. Add the Header.php file into your template, if you have a modified header.php, copy the code for the Header into your header.php (Everything between the comments for Header)
4. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Does the plugin work on a multi-site? =

Yes, All modules work through multi-site. If you require to change the rights of a superuser, go to your multisite network settings.

== Screenshots ==


== Changelog ==

= 1.0 =
* First release! Added 3 useful modules.

== A brief Markdown Example ==

* Webuser User Rights Module: Allows for custom rights on every user.
* Google Login API: Allows for Google Login in WP-ADMIN (Follow instructions)
* Webuser Custom Headers: Allows users to choose images in the header, multiple images show a slider
