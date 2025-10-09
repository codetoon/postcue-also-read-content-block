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
$source = !empty($attributes['allowCustomStyle']) ? $attributes : postcue_alsoread_get_global_defaults();

$blockTitle = $source['blockTitle'] ?? '';
$blockTitleTextColor = $source['blockTitleTextColor'] ?? '';
$blockTitleFontSize = $source['blockTitleFontSize'] ?? '';
$postTitleTextColor = $source['postTitleTextColor'] ?? '';
$postTitleFontSize = $source['postTitleFontSize'] ?? '';
$postBgColor = $source['postBgColor'] ?? '';


$postTitle = $selectedPost['title'] ?? '';
$postLink = $selectedPost['link'] ?? '#';
$postThumbnail = $selectedPost['thumbnail'] ?? '';

?>
<div id="postcue-also-read-content-block" <?php echo esc_attr(get_block_wrapper_attributes()); ?>>
   <h2 class="display-posts-title" style="color: <?php echo esc_attr($blockTitleTextColor); ?> !important; font-size: <?php echo esc_attr($blockTitleFontSize); ?> !important;"><?php echo esc_html($blockTitle); ?></h2>
     <ul class="display-posts-listing">
        <li class="listing-item" style="background-color: <?php echo esc_attr($postBgColor); ?> !important;">
        <?php if($postThumbnail): ?>
            <a class="image" target="_blank" href="<?php echo esc_url($postLink); ?>">
                    <img width="150" height="150" src="<?php echo esc_url($postThumbnail); ?>" alt="<?php echo esc_html($postTitle); ?>" />
            </a>
        <?php endif;?>
        <a class="title" target="_blank" href="<?php echo esc_url($postLink); ?>" style="color: <?php echo esc_attr($postTitleTextColor); ?> !important; font-size: <?php echo esc_attr($postTitleFontSize); ?> !important;"><?php echo esc_html($postTitle); ?></a>
        </li>
     </ul>
</div>