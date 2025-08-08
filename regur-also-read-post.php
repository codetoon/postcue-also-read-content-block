<?php
/**
 * Plugin Name:       Regur Also Read Post
 * Description:       A simple plugin to display also read posts.
 * Version:           0.1.0
 * Requires at least: 6.7
 * Requires PHP:      7.4
 * Author:            <a href="https://www.regur.net">Regur Technology Solutions</a>
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       regur-also-read-post
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
function create_block_regur_also_read_post_block_init() {
	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` and registers the block type(s)
	 * based on the registered block metadata.
	 * Added in WordPress 6.8 to simplify the block metadata registration process added in WordPress 6.7.
	 *
	 * @see https://make.wordpress.org/core/2025/03/13/more-efficient-block-type-registration-in-6-8/
	 */
	if ( function_exists( 'wp_register_block_types_from_metadata_collection' ) ) {
		wp_register_block_types_from_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
		return;
	}

	/**
	 * Registers the block(s) metadata from the `blocks-manifest.php` file.
	 * Added to WordPress 6.7 to improve the performance of block type registration.
	 *
	 * @see https://make.wordpress.org/core/2024/10/17/new-block-type-registration-apis-to-improve-performance-in-wordpress-6-7/
	 */
	if ( function_exists( 'wp_register_block_metadata_collection' ) ) {
		wp_register_block_metadata_collection( __DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php' );
	}
	/**
	 * Registers the block type(s) in the `blocks-manifest.php` file.
	 *
	 * @see https://developer.wordpress.org/reference/functions/register_block_type/
	 */
	$manifest_data = require __DIR__ . '/build/blocks-manifest.php';
	foreach ( array_keys( $manifest_data ) as $block_type ) {
		register_block_type( __DIR__ . "/build/{$block_type}" );
	}
}
add_action( 'init', 'create_block_regur_also_read_post_block_init' );

// Register AJAX handlers for both logged-in and guest users
add_action('wp_ajax_post_search', 'rps_ajax_post_search');
add_action('wp_ajax_nopriv_post_search', 'rps_ajax_post_search');

function rps_ajax_post_search()
{
	error_log('AJAX called');

	$term = isset($_GET['term']) ? sanitize_text_field($_GET['term']) : ''; // Sanitize the search term

	error_log('Search term: ' . $term); 

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

	error_log('Results found: ' . count($results));

	wp_send_json($results);
}
// Helper to get global default styles
function rps_get_global_defaults() {
    return get_option('regur_also_read_post_defaults', [
        'blockTitle' => 'Also Read',
        'textColor' => '#696969',
        'fontSize' => '18px',
        'postTitleTextColor' => '#ffffff',
        'postTitleFontSize' => '18px',
        'postBgColor' => '#06b7d3',
    ]);
}

// Enqueue scripts for the frontend
function rps_enqueue_scripts() {
    wp_enqueue_script(
        'rps-search',
        plugin_dir_url(__FILE__) . 'view.js',
        ['jquery'],
        null,
        true
    );

    wp_localize_script('rps-search', 'rps_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'defaults' => rps_get_global_defaults(),
    ]);
}
add_action('wp_enqueue_scripts', 'rps_enqueue_scripts');

// Enqueue in block editor too
function rps_enqueue_editor_scripts() {
    $defaults = rps_get_global_defaults();
    wp_add_inline_script(
        'wp-block-editor',
        'window.ajaxurl = "' . admin_url('admin-ajax.php') . '";window.rpsDefaults = ' . json_encode($defaults) . ';',
        'before'
    );
}
add_action('enqueue_block_editor_assets', 'rps_enqueue_editor_scripts');
