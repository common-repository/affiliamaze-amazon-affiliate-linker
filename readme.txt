=== AffiliAmaze - Affiliate Linker ===
Contributors: qing999
Donate link: https://www.paypal.com/donate/?hosted_button_id=BYTYFM8J9B7H6
Tags: affiliate, amazon, link
Requires at least: 5.3
Tested up to: 6.6
Requires PHP: 7.4
Stable tag: 3.5
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

AffiliAmaze helps you promote affiliate products on your WordPress site using a simple shortcode interface.

== Description ==
This plugin allows you to promote affiliate products on your WordPress pages and posts through a simple shortcode interface. Create attractive product boxes that contain affiliate links automatically associated with your Amazon Affiliate Associate account.

== Installation ==
1. Upload the plugin files to the "/wp-content/plugins/plugin-name" directory, or install the plugin directly through the WordPress plugins screen.

2. Activate the plugin through the 'Plugins' screen in WordPress.

3. Use the shortcode [AffiliAmaze product_id="YOUR_PRODUCT_ID"] on your pages or posts to add your affiliate products.

== Frequently Asked Questions ==
**Question:** How do I insert an affiliate product using the shortcode?
**Answer:** Use the shortcode `[affiliamaze_affiliate_link product_id=""]` in your WordPress editor to insert an affiliate product. You can also specify additional parameters such as product ID and layout options.

**Question:** Where do I find the product ID?
**Answer:** The product ID can be found under the "Affiliate Products" menu for each created affiliate product within your WordPress dashboard.

**Question:** Can I place multiple affiliate products on the same page?
**Answer:** Yes, you can place as many affiliate products as you like on a single page by inserting the shortcode in different locations.

**Question:** Does the plugin support various affiliate programs besides Amazon?
**Answer:** Currently, the plugin primarily focuses on the Amazon Affiliate Program. However, future versions may add support for other affiliate programs.

**Question:** Can I add my own products as affiliate products?
**Answer:** Yes, the plugin allows you to manage affiliate products, including custom products not available on Amazon. You can manage these products with the plugin and generate affiliate links for them.

**Question:** Does the plugin support tracking clicks and conversions for affiliate links?
**Answer:** The plugin itself does not provide built-in functionality for tracking clicks and conversions. However, you can use external tracking services or tools provided by your affiliate program provider to monitor these metrics.

== Screenshots ==
1. An example of an affiliate product box on a page.

== Changelog ==
= 2.7 =
* Transitioned from Elementor widget to shortcode.
* Added support for affiliate product management.
* Improved CSS styles.

= 2.9 =
* Added ability to import and export affiliate products.
* Implemented support for automatic generation of JSON files for affiliate product import and export.
* Introduced new settings page for configuring Amazon affiliate IDs and button texts.
* Added custom post type support for affiliate products.
* Provided customizable options for displaying Prime logo and Bestseller label.

= 3.0 =
* Escaped variables and options when echo'd to enhance security and prevent vulnerabilities.
* Updated text domains to ensure they match the plugin slug for proper internationalization.

= 3.2 =
* Fixed a syntax error in the `add_meta_box` function.
* Sanitized, validated, and escaped all input and output data to enhance security.
* Replaced deprecated `html_entity_decode()` usage to avoid passing null values.

= 3.3 =
* Sanitized, validated, and escaped all input and output data to enhance security.

= 3.4 =
* Update to include JSON export feature

= 3.5 =
* New Feature: Affiliate Market Column in Affiliate Products Overview

== Upgrade Notice ==
= 2.7 =
We have transitioned to shortcode usage for embedding affiliate product frames. Please update your pages and posts accordingly.

= 2.9 =
We have introduced new features for managing affiliate products, including import/export functionality and customizable settings. Please update your plugin settings accordingly.

= 3.5 =
A new column, "Affiliate Market," has been added to the affiliate products overview table. This will allow you to quickly see and manage the market for each affiliate product directly from the overview page.
