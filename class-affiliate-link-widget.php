<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


// Ensure the function doesn't already exist
if (!function_exists('affiliate_linker_affiliamaze_affiliate_link_shortcode')) {

    function affiliate_linker_affiliamaze_affiliate_link_shortcode($atts) {
        // Extract shortcode attributes
        $atts = shortcode_atts(
            array(
                'product_id' => '',
            ),
            $atts
        );

        ob_start();

        if (!empty($atts['product_id'])) {
            $post_id = $atts['product_id'];
            $image_url = get_post_meta($post_id, 'affiliate_image_url', true);
            $title = get_post_meta($post_id, 'affiliate_product_title', true);
            $amazon_url = get_post_meta($post_id, 'affiliate_amazon_url', true);
            $price = get_post_meta($post_id, 'affiliate_price', true);
            $show_prime_logo = get_post_meta($post_id, 'affiliate_show_prime_logo', true);
            $show_bestseller_label = get_post_meta($post_id, 'affiliate_show_bestseller_label', true);
            $market = get_post_meta($post_id, 'affiliate_amazon_market', true);
            $affiliate_id = get_option('affiliate_' . $market . '_id');
            $affiliate_link = $amazon_url . '?tag=' . $affiliate_id;

            // Get the button text based on the selected Amazon region
            $button_text = get_option('affiliate_button_text_' . $market, 'Buy on Amazon');

            echo '<div class="affiliate-frame">';
            if ($show_bestseller_label === 'yes') {
                echo '<div class="affiliate-badge">' . esc_html__('BESTSELLER', 'affiliamaze-amazon-affiliate-linker') . '</div>'; // Text domain added here
            }
            echo '<div class="affiliate-book">';
            echo '<img class="affiliate-image" src="' . esc_url($image_url) . '" alt="' . esc_attr($title) . '" />';
            echo '<div class="affiliate-info">';
            echo '<h3 class="affiliate-title">' . esc_html($title) . '</h3>';
            if ($show_prime_logo === 'yes') {
                echo '<p class="affiliate-price">' . esc_html($price) . ' <img class="prime-logo" src="' . esc_url(plugin_dir_url(__FILE__) . 'images/prime-logo.svg') . '" alt="' . esc_html__('Amazon Prime Logo', 'affiliamaze-amazon-affiliate-linker') . '" /></p>'; // Text domain added here
            } else {
                echo '<p class="affiliate-price">' . esc_html($price) . '</p>';
            }
            echo '<div class="mehrcontainer">';
            echo '<a class="amazonbutton" href="' . esc_url($affiliate_link) . '" target="_blank">';
            echo '<img src="' . esc_url(plugin_dir_url(__FILE__) . 'images/amazon-icon.svg') . '" alt="' . esc_html__('Amazon-Icon', 'affiliamaze-amazon-affiliate-linker') . '" style="height: 20px; vertical-align: middle; margin-right: 10px;"/>'; // Text domain added here
            echo esc_html($button_text); // Use dynamic button text
            echo '</a>';
            echo '</div>'; // .mehrcontainer
            echo '</div>'; // .affiliate-info
            echo '</div>'; // .affiliate-book
            echo '</div>'; // .affiliate-frame
        } else {
            echo '<p>' . esc_html__('Please select an affiliate product.', 'affiliamaze-amazon-affiliate-linker') . '</p>'; // Text domain added here
        }

        return ob_get_clean();
    }

    add_shortcode('AffiliAmaze', 'affiliate_linker_affiliamaze_affiliate_link_shortcode');

    function affiliate_linker_affiliate_link_shortcode_styles() {
        wp_enqueue_style('affiliate-link-shortcode-style', plugin_dir_url(__FILE__) . 'affiliate-link-style.css');
    }
    add_action('wp_enqueue_scripts', 'affiliate_linker_affiliate_link_shortcode_styles');
}
?>
