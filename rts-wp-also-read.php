<?php
/**
 * Plugin Name:       PostCue Also Read Content Block
 * Plugin URI: https://wpalsoread.com/
 * Description: Add "Also Read" blocks to your posts for engaging and visually appealing interlinked content that keeps readers exploring and improves SEO.
 * Version:           1.0.0
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author: Regur Technology Solutions
 * Author URI: https://www.regur.net/            
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       postcue-also-read
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path(__FILE__) . 'admin/settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';

function rtswpar_create_block_init() {
register_block_type( __DIR__ . '/build/rts-wp-also-read' );
}

add_action( 'init', 'rtswpar_create_block_init' );

// Register AJAX handlers for both logged-in and guest users
add_action('wp_ajax_post_search', 'rtswpar_ajax_post_search');
add_action('wp_ajax_nopriv_post_search', 'rtswpar_ajax_post_search');

function rtswpar_ajax_post_search()
{
    // Safely retrieve and sanitize nonce
    $nonce = isset($_GET['_rtswparnonce']) ? sanitize_text_field( wp_unslash( $_GET['_rtswparnonce'] ) ) : '';

    // Verify nonce
    if ( ! wp_verify_nonce( $nonce, 'rtswpar_post_search' ) ) {
        wp_send_json_error( 'Invalid request (nonce verification failed)' );
        return;
    }

	// Capability check for logged-in users
    if ( is_user_logged_in() && ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Insufficient permissions.' );
        return;
    }
	// Unslash and sanitize
    $term = isset($_GET['term']) ? sanitize_text_field( wp_unslash($_GET['term']) ) : '';
    $cache_key = 'rtswpar_post_search_' . md5($term);
    $results = get_transient($cache_key);

    if ($results === false) {
        $query = new WP_Query([
            's' => $term,
            'post_type' => 'post',
            'post_status' => 'publish',
            'posts_per_page' => 100,
        ]);
        $results = [];
	    // Check if the query has posts
        if ($query->have_posts()) {
            while ($query->have_posts()) {
                $query->the_post();
			// Collect the post data
                $results[] = [
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'link' => get_permalink(),
                    'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: '',
                ];
            }
        }
        set_transient($cache_key, $results, 60 * 5); // Cache for 5 minutes
    }

    wp_send_json($results);
}

// Remove frontend script enqueue (no view.js, no localization)

// Enqueue in block editor only
function rtswpar_enqueue_editor_scripts() {
    $defaults = rtswpar_get_global_defaults();
    wp_add_inline_script(
        'wp-block-editor',
        'window.ajaxurl = "' . admin_url('admin-ajax.php') . '";' .
        'window.rtswparbDefaults = ' . json_encode($defaults) . ';' .
        'window.rtswparbNonce = "' . wp_create_nonce('rtswpar_post_search') . '";',
        'before'
    );
}

add_action('enqueue_block_editor_assets', 'rtswpar_enqueue_editor_scripts');

function my_plugin_add_settings_link( $links ) {
    $settings_link = '<a href="' . get_admin_url(null, 'admin.php?page=rts-wp-also-read-settings') . '">' . __('Settings', 'wp-also-read') . '</a>';
    array_push( $links, $settings_link );
    return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'my_plugin_add_settings_link' );
