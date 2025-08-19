<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
require_once plugin_dir_path(__DIR__) . '../includes/functions.php';


$attributes = $attributes ?? [];
$selectedPost = $attributes['selectedPost'] ?? null;

if(empty($selectedPost)){
    return '';
}

// Use block attributes if allowCustomStyle is true, else use global defaults
$source = !empty($attributes['allowCustomStyle']) ? $attributes : rtswpar_get_global_defaults();

$blockTitle = $source['blockTitle'] ?? '';
$blockTitleTextColor = $source['blockTitleTextColor'] ?? '';
$blockTitleFontSize = $source['blockTitleFontSize'] ?? '';
$postTitleTextColor = $source['postTitleTextColor'] ?? '';
$postTitleFontSize = $source['postTitleFontSize'] ?? '';
$postBgColor = $source['postBgColor'] ?? '';


$title = $selectedPost['title'] ?? '';
$link = $selectedPost['link'] ?? '#';
$thumbnail = $selectedPost['thumbnail'] ?? '';

?>
<div <?php echo esc_attr(get_block_wrapper_attributes()); ?>>
   <h2 class="display-posts-title" style="color: <?php echo esc_attr($blockTitleTextColor); ?> !important; font-size: <?php echo esc_attr($blockTitleFontSize); ?> !important;"><?php echo esc_html__($blockTitle, 'rts-wp-also-read'); ?></h2>
     <ul class="display-posts-listing">
        <li class="listing-item" style="background-color: <?php echo esc_attr($postBgColor); ?> !important;">
        <?php if($thumbnail): ?>
            <a class="image" target="_blank" href="<?php echo esc_url($link); ?>">
                    <img width="150" height="150" src="<?php echo esc_url($thumbnail); ?>" class="attachment-thumbnail size-thumbnail wp-post-image" alt="<?php echo esc_html($title); ?>" />
            </a>
        <?php endif;?>
        <a class="title" target="_blank" href="<?php echo esc_url($link); ?>" style="color: <?php echo esc_attr($postTitleTextColor); ?> !important; background-color: <?php echo esc_attr($postBgColor); ?> !important; font-size: <?php echo esc_attr($postTitleFontSize); ?> !important;"><?php echo esc_html__($title, 'rts-wp-also-read'); ?></a>
        </li>
     </ul>
</div>