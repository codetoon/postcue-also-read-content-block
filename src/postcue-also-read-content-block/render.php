<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
require_once plugin_dir_path(__DIR__) . '../includes/functions.php';


$pocualrecb_attributes = $attributes ?? [];
$pocualrecb_selectedPost = $pocualrecb_attributes['selectedPost'] ?? null;

if(empty($pocualrecb_selectedPost)){
    return '';
}

// Use block attributes if allowCustomStyle is true, else use global defaults
$pocualrecb_source = !empty($pocualrecb_attributes['allowCustomStyle']) ? $pocualrecb_attributes : pocualrecb_get_global_defaults();

$pocualrecb_block_title = $pocualrecb_source['blockTitle'] ?? '';
$pocualrecb_block_title_text_color = $pocualrecb_source['blockTitleTextColor'] ?? '';
$pocualrecb_block_title_font_size = $pocualrecb_source['blockTitleFontSize'] ?? '';
$pocualrecb_post_title_text_color = $pocualrecb_source['postTitleTextColor'] ?? '';
$pocualrecb_post_title_font_size = $pocualrecb_source['postTitleFontSize'] ?? '';
$pocualrecb_post_bg_color = $pocualrecb_source['postBgColor'] ?? '';


$pocualrecb_post_title = $pocualrecb_selectedPost['title'] ?? '';
$pocualrecb_post_link = $pocualrecb_selectedPost['link'] ?? '#';
$pocualrecb_post_thumbnail = $pocualrecb_selectedPost['thumbnail'] ?? '';

?>
<div id="postcue-also-read-content-block" <?php echo esc_attr(get_block_wrapper_attributes()); ?>>
   <strong class="postcue-also-read-content-block-title" style="color: <?php echo esc_attr($pocualrecb_block_title_text_color); ?> !important; font-size: <?php echo esc_attr($pocualrecb_block_title_font_size); ?> !important;"><?php echo esc_html($pocualrecb_block_title); ?></strong>
     <ul class="postcue-also-read-content-block-post-listing">
        <li class="postcue-also-read-content-block-listing-item" style="background-color: <?php echo esc_attr($pocualrecb_post_bg_color); ?> !important;">
        <?php if($pocualrecb_post_thumbnail): ?>
            <a class="postcue-also-read-content-block-post-image" target="_blank" href="<?php echo esc_url($pocualrecb_post_link); ?>">
                    <img width="150" height="150" src="<?php echo esc_url($pocualrecb_post_thumbnail); ?>" alt="<?php echo esc_html($pocualrecb_post_title); ?>" />
            </a>
        <?php endif;?>
        <a class="postcue-also-read-content-block-post-title" target="_blank" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important; font-size: <?php echo esc_attr($pocualrecb_post_title_font_size); ?> !important;"><?php echo esc_html($pocualrecb_post_title); ?></a>
        </li>
     </ul>
</div>