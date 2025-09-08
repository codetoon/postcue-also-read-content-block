<?php
/**
 * Plugin Name:       WP Also Read
 * Description:       A simple plugin to display also read posts.
 * Version:           0.1.0
 * Requires at least: 5.9
 * Requires PHP:      7.4
 * Author:            
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       rts-wp-also-read
 *
 * @package CreateBlock
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
/**
 * Registers the block using a `blocks-manifest.php` file, which improves the performance of block type registration.
 * Behind the scenes, it also registers all assets so they can be enqueued
 * through the block editor in the corresponding context.
 *
 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
 */
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

	$query = new WP_Query([ // Create a new WP_Query instance
		's' => $term,
		'post_type' => 'post',
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
