<?php
/*
 * Plugin Name:      AffiliAmaze - Affiliate Linker
 * Plugin URI:       -
 * Description:      A plugin for creating affiliate link frames for Amazon Products via shortcode and managing affiliate products. Use the shortcode [AffiliAmaze product_id="YOUR_PRODUCT_ID"] on your pages or posts to add your affiliate products.
 * Version:          3.5
 * Requires at least: 5.3
 * Requires PHP:     7.4
 * Author:           qing999
 * License:          GPL v2 or later
 * License URI:      https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:      affiliamaze-amazon-affiliate-linker
 * Domain Path:      /languages
 */
 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
 
// Include the file containing the shortcode logic
require_once plugin_dir_path(__FILE__) . 'class-affiliate-link-widget.php';


// Add columns for the product ID and Amazon Market in the summary table
function affiliate_linker_manage_affiliate_product_posts_columns($columns) {
    $columns['affiliate_product_id'] = __('Product ID', 'affiliamaze-amazon-affiliate-linker');
    $columns['affiliate_amazon_market'] = __('Amazon Market', 'affiliamaze-amazon-affiliate-linker');
    return $columns;
}
add_filter('manage_affiliate_product_posts_columns', 'affiliate_linker_manage_affiliate_product_posts_columns');


// Display the product ID and Amazon Market values in the new columns
function affiliate_linker_manage_affiliate_product_posts_custom_column($column, $post_id) {
    if ($column === 'affiliate_product_id') {
        echo esc_html((int) $post_id);
    } elseif ($column === 'affiliate_amazon_market') {
        $amazon_market = get_post_meta($post_id, 'affiliate_amazon_market', true); // Corrected meta key to 'affiliate_amazon_market'
        echo esc_html($amazon_market);
    }
}
add_action('manage_affiliate_product_posts_custom_column', 'affiliate_linker_manage_affiliate_product_posts_custom_column', 10, 2);



// Add metaboxes
function affiliate_linker_add_affiliate_product_metaboxes() {
    add_meta_box(
        'affiliate_product_details',
        esc_html__('AffiliAmaze Product Details', 'affiliamaze-amazon-affiliate-linker'),
        'affiliate_linker_affiliate_product_meta_box',
        'affiliate_product'
    );
}
add_action('add_meta_boxes', 'affiliate_linker_add_affiliate_product_metaboxes');

function affiliate_linker_affiliate_product_meta_box($post) {
    wp_nonce_field('affiliate_product_nonce', 'affiliate_product_nonce');

    // Retrieve metadata and sanitize
    $image_url = sanitize_text_field(get_post_meta($post->ID, 'affiliate_image_url', true));
    $product_title = sanitize_text_field(get_post_meta($post->ID, 'affiliate_product_title', true));
    $amazon_url = esc_url_raw(get_post_meta($post->ID, 'affiliate_amazon_url', true));
    $price = sanitize_text_field(get_post_meta($post->ID, 'affiliate_price', true));
    $show_prime_logo = sanitize_text_field(get_post_meta($post->ID, 'affiliate_show_prime_logo', true));
    $show_bestseller_label = sanitize_text_field(get_post_meta($post->ID, 'affiliate_show_bestseller_label', true));
    $selected_market = sanitize_text_field(get_post_meta($post->ID, 'affiliate_amazon_market', true)) ?: 'de';

    // Adjust button text and affiliate ID based on selected market
	$button_text = sanitize_text_field(get_option('affiliate_button_text_' . $selected_market, __('Buy on Amazon', 'affiliamaze-amazon-affiliate-linker')));
	$affiliate_id = sanitize_text_field(get_option('affiliate_' . $selected_market . '_id'));
    $affiliate_link = esc_url($amazon_url . '?tag=' . $affiliate_id);

	// Metabox HTML for entering product information
	echo '<label for="affiliate_product_id">' . esc_html__('Product ID:', 'affiliamaze-amazon-affiliate-linker') . '</label>';
	echo '<input type="text" id="affiliate_product_id" name="affiliate_product_id" value="' . esc_attr($post->ID) . '" class="widefat" readonly>';
	
    echo '<label for="affiliate_image_url">' . esc_html__('Image URL:', 'affiliamaze-amazon-affiliate-linker') . '</label>'; // Text domain added here
    echo '<input type="text" id="affiliate_image_url" name="affiliate_image_url" value="' . esc_attr($image_url) . '" class="widefat">';

    echo '<label for="affiliate_product_title">' . esc_html__('Product title:', 'affiliamaze-amazon-affiliate-linker') . '</label>'; // Text domain added here
    echo '<input type="text" id="affiliate_product_title" name="affiliate_product_title" value="' . esc_attr($product_title) . '" class="widefat">';

    echo '<label for="affiliate_amazon_url">' . esc_html__('Amazon URL:', 'affiliamaze-amazon-affiliate-linker') . '</label>'; // Text domain added here
    echo '<input type="text" id="affiliate_amazon_url" name="affiliate_amazon_url" value="' . esc_attr($amazon_url) . '" class="widefat">';

    // View Amazon URL with Affiliate ID
    echo '<label for="affiliate_amazon_url_with_id">' . esc_html__('Amazon URL + Affiliate ID:', 'affiliamaze-amazon-affiliate-linker') . '</label>'; // Text domain added here
    echo '<input type="text" id="affiliate_amazon_url_with_id" name="affiliate_amazon_url_with_id" value="' . esc_attr($affiliate_link) . '" class="widefat" readonly>';

    echo '<label for="affiliate_price">' . esc_html__('Price:', 'affiliamaze-amazon-affiliate-linker') . '</label>'; // Text domain added here
    echo '<input type="text" id="affiliate_price" name="affiliate_price" value="' . esc_attr($price) . '" class="widefat">';

    // Add read-only field for button text
    echo '<label for="affiliate_button_text">' . esc_html__('Button text:', 'affiliamaze-amazon-affiliate-linker') . '</label>'; // Text domain added here
    echo '<input type="text" id="affiliate_button_text" name="affiliate_button_text" value="' . esc_attr($button_text) . '" class="widefat" readonly>';

    echo '<label for="affiliate_amazon_market">' . esc_html__('Amazon market:', 'affiliamaze-amazon-affiliate-linker') . '</label>'; // Text domain added here
    echo '<select id="affiliate_amazon_market" name="affiliate_amazon_market" class="widefat">';
    
	// List of supported markets
    $markets = ['de' => esc_html__('Amazon.de', 'affiliamaze-amazon-affiliate-linker'), 'uk' => esc_html__('Amazon.co.uk', 'affiliamaze-amazon-affiliate-linker'), 'fr' => esc_html__('Amazon.fr', 'affiliamaze-amazon-affiliate-linker'), 'en' => esc_html__('Amazon.com', 'affiliamaze-amazon-affiliate-linker'), 'ca' => esc_html__('Amazon.ca', 'affiliamaze-amazon-affiliate-linker'), 'mx' => esc_html__('Amazon.mx', 'affiliamaze-amazon-affiliate-linker')];
    foreach ($markets as $market_code => $market_name) {
        echo '<option value="' . esc_attr($market_code) . '" ' . selected($selected_market, $market_code, false) . '>' . esc_html($market_name) . '</option>';
    }
    echo '</select>';

    echo '<label for="affiliate_show_prime_logo">' . esc_html__('Show Prime logo:', 'affiliamaze-amazon-affiliate-linker') . '</label>'; // Text domain added here
    echo '<select id="affiliate_show_prime_logo" name="affiliate_show_prime_logo" class="widefat">';
    echo '<option value="yes" ' . selected($show_prime_logo, 'yes', false) . '>' . esc_html__('Yes', 'affiliamaze-amazon-affiliate-linker') . '</option>'; // Text domain added here
    echo '<option value="no" ' . selected($show_prime_logo, 'no', false) . '>' . esc_html__('No', 'affiliamaze-amazon-affiliate-linker') . '</option>'; // Text domain added here
    echo '</select>';

    // Add Bestseller label option
    echo '<label for="affiliate_show_bestseller_label">' . esc_html__('Show Bestseller Label:', 'affiliamaze-amazon-affiliate-linker') . '</label>'; // Text domain added here
    echo '<select id="affiliate_show_bestseller_label" name="affiliate_show_bestseller_label" class="widefat">';
    echo '<option value="yes" ' . selected($show_bestseller_label, 'yes', false) . '>' . esc_html__('Yes', 'affiliamaze-amazon-affiliate-linker') . '</option>'; // Text domain added here
    echo '<option value="no" ' . selected($show_bestseller_label, 'no', false) . '>' . esc_html__('No', 'affiliamaze-amazon-affiliate-linker') . '</option>'; // Text domain added here
    echo '</select>';
}

// Sanitization, validation, and escaping for nonces
function affiliate_linker_save_affiliate_product_meta_box($post_id) {
    // Check if the nonce is set and verified
    if (!isset($_POST['affiliate_product_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['affiliate_product_nonce'])), 'affiliate_product_nonce')) {
        return;
    }

    // Check if the user has permission to edit the post
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    // Save selected market choice
    if (isset($_POST['affiliate_amazon_market'])) {
        update_post_meta($post_id, 'affiliate_amazon_market', sanitize_text_field($_POST['affiliate_amazon_market']));
        // Assign corresponding affiliate ID based on market choice
        $market_code = sanitize_text_field($_POST['affiliate_amazon_market']);
        $affiliate_id = sanitize_text_field(get_option('affiliate_' . $market_code . '_id'));
        update_post_meta($post_id, 'affiliate_amazon_id', $affiliate_id);
    }

    // Check for empty fields except price
    $required_fields = ['affiliate_image_url', 'affiliate_product_title', 'affiliate_amazon_url', 'affiliate_amazon_market', 'affiliate_show_prime_logo'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || trim($_POST[$field]) === '') {
            // Add error message or other actions
            return; // Stops saving if any required field is empty
        }
    }

    // Save metadata
    update_post_meta($post_id, 'affiliate_image_url', sanitize_text_field($_POST['affiliate_image_url']));
    update_post_meta($post_id, 'affiliate_product_title', sanitize_text_field($_POST['affiliate_product_title']));
    update_post_meta($post_id, 'affiliate_amazon_url', esc_url_raw($_POST['affiliate_amazon_url']));
    update_post_meta($post_id, 'affiliate_price', sanitize_text_field($_POST['affiliate_price']));
    update_post_meta($post_id, 'affiliate_show_prime_logo', sanitize_text_field($_POST['affiliate_show_prime_logo']));

    // Save Bestseller label setting
    if (isset($_POST['affiliate_show_bestseller_label'])) {
        update_post_meta($post_id, 'affiliate_show_bestseller_label', sanitize_text_field($_POST['affiliate_show_bestseller_label']));
    }
}
add_action('save_post', 'affiliate_linker_save_affiliate_product_meta_box');

// Add JavaScript and CSS
function affiliate_linker_enqueue_scripts() {
    wp_enqueue_script('affiliate-linker-script', plugin_dir_url(__FILE__) . 'js/affiliate-linker.js', array('jquery'), '1.0.0', true);
}
add_action('wp_enqueue_scripts', 'affiliate_linker_enqueue_scripts');

function affiliate_linker_enqueue_styles() {
    wp_enqueue_style('affiliate-linker-style', plugin_dir_url(__FILE__) . 'css/affiliate-linker.css', array(), '1.0.0', 'all');
}
add_action('wp_enqueue_scripts', 'affiliate_linker_enqueue_styles');

function affiliate_linker_enqueue_admin_scripts() {
    wp_enqueue_script('affiliate-linker-admin-script', plugin_dir_url(__FILE__) . 'js/admin-affiliate-linker.js', array('jquery'), '1.0.0', true);
    wp_enqueue_style('affiliate-linker-admin-style', plugin_dir_url(__FILE__) . 'css/admin-affiliate-linker.css', array(), '1.0.0', 'all');
}
add_action('admin_enqueue_scripts', 'affiliate_linker_enqueue_admin_scripts');

// Custom Post Type for affiliate products and add it to admin menu
function affiliate_linker_create_affiliate_product_cpt_and_menu() {
    $args = array(
        'public' => true,
        'label' => __('Affiliate Products', 'affiliamaze-amazon-affiliate-linker'),
        'supports' => array('title'),
    );
    register_post_type('affiliate_product', $args);
    
    // Add parent menu
    add_menu_page(
        __('AffiliAmaze', 'affiliamaze-amazon-affiliate-linker'), // Updated to "AffiliAmaze"
        __('AffiliAmaze', 'affiliamaze-amazon-affiliate-linker'), // Updated to "AffiliAmaze"
        'manage_options',
        'affiliamaze-info', // Updated to 'affiliamaze-info' for the Info submenu
        'affiliate_linker_render_info_page', // Updated callback function
        'dashicons-admin-links'
    );

    // Adding the Info submenu
    add_submenu_page(
        'affiliamaze-info', // Parent slug
        __('Info', 'affiliamaze-amazon-affiliate-linker'), // Menu title
        __('Info', 'affiliamaze-amazon-affiliate-linker'), // Menu title
        'manage_options', // Capability
        'affiliamaze-info', // Menu slug
        'affiliate_linker_render_info_page' // Callback function
    );

    // Adding the Affiliate Products submenu
    add_submenu_page(
        'affiliamaze-info', // Parent slug
        __('Affiliate Products', 'affiliamaze-amazon-affiliate-linker'), // Menu title
        __('Affiliate Products', 'affiliamaze-amazon-affiliate-linker'), // Menu title
        'manage_options', // Capability
        'edit.php?post_type=affiliate_product' // Menu slug
    );

    // Adding the Settings submenu    
    add_submenu_page(
        'affiliamaze-info', // Parent slug
        __('Settings', 'affiliamaze-amazon-affiliate-linker'), // Menu title
        __('Settings', 'affiliamaze-amazon-affiliate-linker'), // Menu title
        'manage_options', // Capability
        'affiliate-link-settings', // Menu slug
        'affiliate_linker_affiliate_link_settings_submenu' // Callback function
    );

    // Adding the Import JSON submenu
    add_submenu_page(
        'affiliamaze-info', // Parent slug
        esc_html__('Import JSON', 'affiliamaze-amazon-affiliate-linker'),
        esc_html__('Import JSON', 'affiliamaze-amazon-affiliate-linker'), 
        'manage_options',
        'affiliate-products-import',
        'affiliate_linker_affiliate_products_import_page'
    );

    // Adding the Export JSON submenu
    add_submenu_page(
        'affiliamaze-info', // Parent slug
        esc_html__('Export JSON', 'affiliamaze-amazon-affiliate-linker'),
        esc_html__('Export JSON', 'affiliamaze-amazon-affiliate-linker'),
        'manage_options',
        'export-json',
        'affiliate_linker_render_export_json_page'
    );
}
add_action('admin_menu', 'affiliate_linker_create_affiliate_product_cpt_and_menu');





// Function to render the information page
function affiliate_linker_render_info_page() {
    ?>
    <div class="wrap">
        <h1>AffiliAmaze Plugin Information</h1>
        <p>Welcome to the AffiliAmaze plugin! Below is a guide on how to use this plugin effectively.</p>

        <!-- Include your usage guide here -->

        <h2>How to Use:</h2>
        <ol>
            <li><strong>Create Affiliate Products:</strong> Go to 'Affiliate Products' and add new products. Enter product details such as image URL, title, Amazon URL, price, etc.</li>
            <li><strong>Insert Affiliate Frames:</strong> Use the [AffiliAmaze] shortcode in your posts or pages to insert affiliate links. Provide the 'product_id' attribute with the ID of the affiliate product you created.<br /> Shortcode: <strong>[AffiliAmaze product_id="YOUR_PRODUCT_ID"]</strong></li>
            <li><strong>Customize Settings:</strong> Visit the 'Settings' page to customize plugin settings such as button text, Amazon affiliate ID, display options, etc.</li>
        </ol>

        <h2>Plugin Settings:</h2>
        <p>To customize plugin settings:</p>
        <ol>
            <li>Go to 'Settings' in the WordPress admin menu.</li>
            <li>Select 'AffiliAmaze Settings'.</li>
            <li>Adjust settings according to your preferences.</li>
            <li>Save changes.</li>
        </ol>

        <h2>Need Help?</h2>
        <p>If you have any questions or need assistance, feel free to contact our support team at <strong>smarthomestarter01@gmail.com.</strong></p>
    </div>
    <?php
}


// Sanitization and validation for nonces in import function
// Function to import products from JSON
function affiliate_linker_affiliate_products_import_page() {
    echo '<h1>' . esc_html__('Import Affiliate Products', 'affiliamaze-amazon-affiliate-linker') . '</h1>';
    echo '<form method="post" enctype="multipart/form-data">';
    wp_nonce_field('import_affiliate_products_action', 'import_affiliate_products_nonce');
    echo '<input type="file" name="import_file" />';
    echo '<input type="submit" class="button button-primary" value="' . esc_html__('Import', 'affiliamaze-amazon-affiliate-linker') . '" />';
    echo '</form>';

	if (isset($_FILES['import_file'])) {
		try {
			// Validate the nonce
			if (!isset($_POST['import_affiliate_products_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['import_affiliate_products_nonce'])), 'import_affiliate_products_action')) {
				throw new Exception(esc_html__('Invalid nonce.', 'affiliamaze-amazon-affiliate-linker'));
			}

			// Check for upload errors
			if ($_FILES['import_file']['error'] !== UPLOAD_ERR_OK) {
				$file_error = sanitize_text_field($_FILES['import_file']['error']);
				throw new Exception(esc_html__('Error uploading file.', 'affiliamaze-amazon-affiliate-linker') . ' ' . esc_html($file_error));
			}

			// File path sanitize
			$file = $_FILES['import_file']['tmp_name']; // Verwenden Sie direkt den temporären Dateipfad
			$file = realpath($file); // Stellen Sie sicher, dass der Pfad korrekt ist

			if (!$file || !file_exists($file)) {
				throw new Exception(esc_html__('File does not exist or path is incorrect.', 'affiliamaze-amazon-affiliate-linker'));
			}

			$imported_data = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

			foreach ($imported_data as $product) {
				if (!isset($product['Product Title']) || !isset($product['Image URL']) || !isset($product['Amazon URL'])) {
					continue;
				}

				$new_post = array(
					'post_title' => sanitize_text_field($product['Product Title']),
					'post_type' => 'affiliate_product',
					'post_status' => 'publish',
				);

				$post_id = wp_insert_post($new_post);

				if ($post_id) {
					update_post_meta($post_id, 'affiliate_image_url', esc_url_raw($product['Image URL']));
					update_post_meta($post_id, 'affiliate_product_title', sanitize_text_field($product['Product Title']));
					update_post_meta($post_id, 'affiliate_amazon_url', esc_url_raw($product['Amazon URL']));
					update_post_meta($post_id, 'affiliate_price', isset($product['Price']) ? sanitize_text_field($product['Price']) : '');
					update_post_meta($post_id, 'affiliate_amazon_market', isset($product['Amazon Market']) ? sanitize_text_field($product['Amazon Market']) : 'de');
					update_post_meta($post_id, 'affiliate_show_prime_logo', isset($product['Show Prime Logo']) ? sanitize_text_field($product['Show Prime Logo']) : 'no');
					update_post_meta($post_id, 'affiliate_show_bestseller_label', isset($product['Show Bestseller Label']) ? sanitize_text_field($product['Show Bestseller Label']) : 'no');

					// Debug output
					echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Imported product: ', 'affiliamaze-amazon-affiliate-linker') . esc_html($product['Product Title']) . ' (ID: ' . esc_html($post_id) . ')</p></div>';
				}
			}

			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__('Products imported successfully.', 'affiliamaze-amazon-affiliate-linker') . '</p></div>';
		} catch (Exception $e) {
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__('Failed to import products: ', 'affiliamaze-amazon-affiliate-linker') . esc_html($e->getMessage()) . '</p></div>';
		}
	}
}

// Ensure to add this function in your plugin
add_action('admin_menu', 'affiliate_linker_create_affiliate_product_cpt_and_menu');




// Nonce security for export function
function affiliate_linker_render_export_json_page() {
    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('Export Affiliate Products as JSON', 'affiliamaze-amazon-affiliate-linker') . '</h1>';

    // Check whether the export button was pressed
	if (isset($_POST['export_json'])) {
		// Security check
		if (!isset($_POST['export_nonce_field']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['export_nonce_field'])), 'export_nonce')) {
			return;
		}

		// Call the export function and get the path to the file
		$file_url = affiliate_linker_export_affiliate_products_to_json();

		// Redirect user to download the file
		echo "<script>window.location.href = '" . esc_url($file_url) . "';</script>";
	}

    // Export button form
    echo '<form method="post">';
    wp_nonce_field('export_nonce', 'export_nonce_field');
    echo '<button type="submit" name="export_json" class="button button-primary">Export</button>';
    echo '</form>';
    echo '</div>';
}

function affiliate_linker_export_affiliate_products_to_json() {
    if (!current_user_can('manage_options')) {
        wp_die(esc_html__('Insufficient permissions', 'affiliamaze-amazon-affiliate-linker'));
    }

    // Collect affiliate product data
    $args = array(
        'post_type' => 'affiliate_product',
        'posts_per_page' => -1,
    );
    $affiliate_products = get_posts($args);

    $data_to_export = array();
    foreach ($affiliate_products as $product) {
        $data_to_export[] = array(
            'Product ID' => $product->ID,
            'Product Title' => get_post_meta($product->ID, 'affiliate_product_title', true),
            'Image URL' => get_post_meta($product->ID, 'affiliate_image_url', true),
            'Amazon URL' => get_post_meta($product->ID, 'affiliate_amazon_url', true),
            'Price' => get_post_meta($product->ID, 'affiliate_price', true),
            'Amazon Market' => get_post_meta($product->ID, 'affiliate_amazon_market', true),
            'Show Prime Logo' => get_post_meta($product->ID, 'affiliate_show_prime_logo', true) === 'yes' ? true : false,
            'Show Bestseller Label' => get_post_meta($product->ID, 'affiliate_show_bestseller_label', true) === 'yes' ? true : false,
            // Add more data here
        );
    }

    // Convert data to JSON
    $json_data = json_encode($data_to_export, JSON_PRETTY_PRINT);

    // Save file in uploads directory
    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['basedir'] . '/affiliate_products_export.json';
    file_put_contents($file_path, $json_data);

    // Return download link
    return $upload_dir['baseurl'] . '/affiliate_products_export.json';
}

// Trigger export when a new product is saved
function affiliate_linker_trigger_export_on_save_post($post_id) {
    // Check if the saved post is an affiliate product
    if (get_post_type($post_id) === 'affiliate_product') {
        // Call the export function
        affiliate_linker_export_affiliate_products_to_json();
    }
}
add_action('save_post', 'affiliate_linker_trigger_export_on_save_post');


// Callback function settings page
function affiliate_linker_affiliate_link_settings_submenu() {
    // Settings page content
    echo '<div class="wrap">';
    echo '<h1>' . esc_html__('AffiliAmaze Settings', 'affiliamaze-amazon-affiliate-linker') . '</h1>';
    echo '<form method="post" action="options.php">';
    settings_fields('affiliate_link_options_group');
    do_settings_sections('affiliate-link-settings');
    submit_button();
    echo '</form>';
    echo '</div>';
}

// Register settings
function affiliate_linker_affiliate_link_register_settings() {
    register_setting('affiliate_link_options_group', 'affiliate_de_id');
    register_setting('affiliate_link_options_group', 'affiliate_uk_id');
    register_setting('affiliate_link_options_group', 'affiliate_fr_id');
    register_setting('affiliate_link_options_group', 'affiliate_en_id');
    register_setting('affiliate_link_options_group', 'affiliate_ca_id');
    register_setting('affiliate_link_options_group', 'affiliate_mx_id');
	
    register_setting('affiliate_link_options_group', 'affiliate_button_text_de');
    register_setting('affiliate_link_options_group', 'affiliate_button_text_uk');
    register_setting('affiliate_link_options_group', 'affiliate_button_text_fr');
    register_setting('affiliate_link_options_group', 'affiliate_button_text_en');
    register_setting('affiliate_link_options_group', 'affiliate_button_text_ca');
    register_setting('affiliate_link_options_group', 'affiliate_button_text_mx');

    // Add settings section
    add_settings_section(
        'affiliate_link_settings_section',
        esc_html__('Affiliate IDs and Button Texts', 'affiliamaze-amazon-affiliate-linker'),
        'affiliate_linker_affiliate_link_settings_section_cb',
        'affiliate-link-settings'
    );

    // Add settings fields for affiliate IDs
    $markets = ['de' => esc_html__('Germany', 'affiliamaze-amazon-affiliate-linker'), 'uk' => esc_html__('United Kingdom', 'affiliamaze-amazon-affiliate-linker'), 'fr' => esc_html__('France', 'affiliamaze-amazon-affiliate-linker'), 'en' => esc_html__('USA', 'affiliamaze-amazon-affiliate-linker'), 'ca' => esc_html__('Canada', 'affiliamaze-amazon-affiliate-linker'), 'mx' => esc_html__('Mexico', 'affiliamaze-amazon-affiliate-linker')];
    foreach ($markets as $market_code => $market_name) {
        add_settings_field(
            'affiliate_' . $market_code . '_id_field',
            esc_html__('Affiliate ID - Amazon.', 'affiliamaze-amazon-affiliate-linker') . $market_code,
            'affiliate_linker_affiliate_id_field_cb',
            'affiliate-link-settings',
            'affiliate_link_settings_section',
            ['market' => $market_code]
        );

        add_settings_field(
            'affiliate_button_text_' . $market_code . '_field',
            esc_html__('Button Text - Amazon.', 'affiliamaze-amazon-affiliate-linker') . $market_code,
            'affiliate_linker_affiliate_button_text_field_cb',
            'affiliate-link-settings',
            'affiliate_link_settings_section',
            ['market' => $market_code]
        );
    }
}

add_action('admin_init', 'affiliate_linker_affiliate_link_register_settings');

// Callbacks for section
function affiliate_linker_affiliate_link_settings_section_cb() {
    echo '<p>' . esc_html__('Enter your Amazon Affiliate IDs and Button Texts below.', 'affiliamaze-amazon-affiliate-linker') . '</p>';
}

// Callback for affiliate ID fields
function affiliate_linker_affiliate_id_field_cb($args) {
    $market = $args['market'];
    $option_name = 'affiliate_' . $market . '_id';
    $id = sanitize_text_field(get_option($option_name));
    echo '<input type="text" id="' . esc_attr($option_name) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($id) . '" />';
}

// Callback for affiliate button text fields
function affiliate_linker_affiliate_button_text_field_cb($args) {
    $market = $args['market'];
    $option_name = 'affiliate_button_text_' . $market;
    $text = sanitize_text_field(get_option($option_name, esc_html__('Buy on Amazon', 'affiliamaze-amazon-affiliate-linker')));
    echo '<input type="text" id="' . esc_attr($option_name) . '" name="' . esc_attr($option_name) . '" value="' . esc_attr($text) . '" />';
}


