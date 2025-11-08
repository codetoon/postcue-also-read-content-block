<?php
/**
 * Plugin Name:       PostCue Also Read Content Block
 * Plugin URI:        https://postcue.regur.net/
 * Description:       Add "Also Read" blocks to your posts for engaging and visually appealing interlinked content that keeps readers exploring and improves SEO.
 * Version:           1.0.0
 * Requires at least: 6.1
 * Requires PHP:      7.4
 * Author:            Regur Technology Solutions
 * Author URI:        https://www.regur.net/            
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       postcue-also-read-content-block
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once plugin_dir_path(__FILE__) . 'admin/settings.php';
require_once plugin_dir_path(__FILE__) . 'includes/functions.php';

function pocualrecb_create_block_init() {
register_block_type( __DIR__ . '/build/postcue-also-read-content-block' );
}

add_action( 'init', 'pocualrecb_create_block_init' );

// Register AJAX handlers for both logged-in and guest users
add_action('wp_ajax_pocualrecb_post_search', 'pocualrecb_ajax_post_search');
add_action('wp_ajax_nopriv_pocualrecb_post_search', 'pocualrecb_ajax_post_search');

function pocualrecb_ajax_post_search()
{
    // Safely retrieve and sanitize nonce
    $pocualrecb_nonce = isset($_GET['_pocualrecb_nonce']) ? sanitize_text_field( wp_unslash( $_GET['_pocualrecb_nonce'] ) ) : '';

    // Verify nonce
    if ( ! wp_verify_nonce( $pocualrecb_nonce, 'pocualrecb_post_search' ) ) {
        wp_send_json_error( 'Invalid request (nonce verification failed)' );
        return;
    }

	// Capability check for logged-in users
    if ( is_user_logged_in() && ! current_user_can( 'edit_posts' ) ) {
        wp_send_json_error( 'Insufficient permissions.' );
        return;
    }
	// Unslash and sanitize
    $pocualrecb_term = isset($_GET['term']) ? sanitize_text_field( wp_unslash($_GET['term']) ) : '';

    $pocualrecb_query = new WP_Query([
        's' => $pocualrecb_term,
        'post_type' => 'post',
        'post_status' => 'publish',
        'posts_per_page' => 100,
    ]);
    $pocualrecb_results = [];
    // Check if the query has posts
    if ($pocualrecb_query->have_posts()) {
        while ($pocualrecb_query->have_posts()) {
            $pocualrecb_query->the_post();
        // Collect the post data
            $pocualrecb_results[] = [
                'id' => get_the_ID(),
                'title' => get_the_title(),
                'link' => get_permalink(),
                'thumbnail' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: '',
            ];
        }
    }


    wp_send_json($pocualrecb_results);
}

// Remove frontend script enqueue (no view.js, no localization)

// Enqueue in block editor only
function pocualrecb_enqueue_editor_scripts() {
    $pocualrecb_defaults = pocualrecb_get_global_defaults();
    wp_add_inline_script(
        'wp-block-editor',
        'window.pocualrecb_ajaxurl = "' . esc_url(admin_url('admin-ajax.php')) . '";' .
        'window.pocualrecb_defaults = ' . wp_json_encode($pocualrecb_defaults) . ';' .
        'window.pocualrecb_nonce = "' . esc_js(wp_create_nonce('pocualrecb_post_search')) . '";',
        'before'
    );
}

add_action('enqueue_block_editor_assets', 'pocualrecb_enqueue_editor_scripts');

function pocualrecb_add_settings_link( $pocualrecb_links ) {
    $pocualrecb_settings_url = get_admin_url(null, 'admin.php?page=postcue-also-read-content-block-settings');
    $pocualrecb_settings_link = '<a href="' . esc_url($pocualrecb_settings_url) . '">' . __('Settings', 'postcue-also-read-content-block') . '</a>';
    
    if (current_user_can('edit_posts')) { // Only show to users who can edit posts
        array_push( $pocualrecb_links, $pocualrecb_settings_link );
    }
    return $pocualrecb_links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'pocualrecb_add_settings_link' );
