<?php

/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once plugin_dir_path(__DIR__) . '../includes/functions.php';

$pocualrecb_attributes = $attributes ?? [];
$pocualrecb_selected_post = $pocualrecb_attributes['selectedPost'] ?? null;

if (empty($pocualrecb_selected_post)) {
    return '';
}

$pocualrecb_global_defaults = pocualrecb_get_global_defaults();
$pocualrecb_template = pocualrecb_sanitize_template($pocualrecb_global_defaults['template'] ?? 'default');
$pocualrecb_global_style_source = pocualrecb_get_styles_for_template($pocualrecb_global_defaults, $pocualrecb_template);

// Use block attributes if allowCustomStyle is true, else use selected template defaults.
$pocualrecb_source = ! empty($pocualrecb_attributes['allowCustomStyle'])
    ? pocualrecb_sanitize_style_settings($pocualrecb_attributes, $pocualrecb_global_style_source, $pocualrecb_template)
    : $pocualrecb_global_style_source;

$pocualrecb_template_class = 'pocualrecb-template-' . sanitize_html_class($pocualrecb_template);
$pocualrecb_wrapper_attributes = get_block_wrapper_attributes(['class' => $pocualrecb_template_class]);

$pocualrecb_block_title = $pocualrecb_source['blockTitle'] ?? '';
$pocualrecb_block_title_text_color = $pocualrecb_source['blockTitleTextColor'] ?? '';
$pocualrecb_block_title_font_size = $pocualrecb_source['blockTitleFontSize'] ?? '';
$pocualrecb_post_title_text_color = $pocualrecb_source['postTitleTextColor'] ?? '';
$pocualrecb_post_title_font_size = $pocualrecb_source['postTitleFontSize'] ?? '';
$pocualrecb_post_bg_color = $pocualrecb_source['postBgColor'] ?? '';

$pocualrecb_post_title = $pocualrecb_selected_post['title'] ?? '';
$pocualrecb_post_link = $pocualrecb_selected_post['link'] ?? '#';
$pocualrecb_post_thumbnail = $pocualrecb_selected_post['thumbnail'] ?? '';
$pocualrecb_has_thumbnail = ! empty($pocualrecb_post_thumbnail);

$pocualrecb_image_markup = static function ($pocualrecb_thumbnail, $pocualrecb_link, $pocualrecb_title) {
    if (empty($pocualrecb_thumbnail)) {
        return '<span class="postcue-also-read-content-block-post-image pocualrecb-image-placeholder" aria-hidden="true"></span>';
    }

    ob_start();
    ?>
    <a class="postcue-also-read-content-block-post-image" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_link); ?>">
        <img width="150" height="150" src="<?php echo esc_url($pocualrecb_thumbnail); ?>" alt="<?php echo esc_attr($pocualrecb_title); ?>" />
    </a>
    <?php

    return trim((string) ob_get_clean());
};

$pocualrecb_arrow_markup = '<span class="pocualrecb-template-arrow" aria-hidden="true">&rarr;</span>';

?>
<div id="postcue-also-read-content-block" <?php echo $pocualrecb_wrapper_attributes; ?>>
    <strong class="postcue-also-read-content-block-title" style="color: <?php echo esc_attr($pocualrecb_block_title_text_color); ?> !important; font-size: <?php echo esc_attr($pocualrecb_block_title_font_size); ?> !important;"><?php echo esc_html($pocualrecb_block_title); ?></strong>
    <ul class="postcue-also-read-content-block-post-listing">
        <?php if ('default' === $pocualrecb_template) : ?>
            <li class="postcue-also-read-content-block-listing-item" style="background-color: <?php echo esc_attr($pocualrecb_post_bg_color); ?> !important;">
                <?php if ($pocualrecb_has_thumbnail) : ?>
                    <a class="postcue-also-read-content-block-post-image" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>">
                        <img width="150" height="150" src="<?php echo esc_url($pocualrecb_post_thumbnail); ?>" alt="<?php echo esc_attr($pocualrecb_post_title); ?>" />
                    </a>
                <?php endif; ?>
                <a class="postcue-also-read-content-block-post-title" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important; font-size: <?php echo esc_attr($pocualrecb_post_title_font_size); ?> !important;"><?php echo esc_html($pocualrecb_post_title); ?></a>
            </li>
        <?php elseif ('soft-card' === $pocualrecb_template) : ?>
            <li class="postcue-also-read-content-block-listing-item pocualrecb-template-layout-soft-card" style="background-color: <?php echo esc_attr($pocualrecb_post_bg_color); ?> !important;">
                <?php echo $pocualrecb_image_markup($pocualrecb_post_thumbnail, $pocualrecb_post_link, $pocualrecb_post_title); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <div class="pocualrecb-template-content">
                    <a class="postcue-also-read-content-block-post-title" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important; font-size: <?php echo esc_attr($pocualrecb_post_title_font_size); ?> !important;"><?php echo esc_html($pocualrecb_post_title); ?></a>
                    <a class="pocualrecb-template-action" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important;"><?php echo esc_html__('Read post', 'postcue-also-read-content-block'); ?></a>
                </div>
            </li>
        <?php elseif ('accent-strip' === $pocualrecb_template) : ?>
            <li class="postcue-also-read-content-block-listing-item pocualrecb-template-layout-accent-strip" style="background-color: <?php echo esc_attr($pocualrecb_post_bg_color); ?> !important;">
                <?php echo $pocualrecb_image_markup($pocualrecb_post_thumbnail, $pocualrecb_post_link, $pocualrecb_post_title); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <a class="postcue-also-read-content-block-post-title" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important; font-size: <?php echo esc_attr($pocualrecb_post_title_font_size); ?> !important;"><?php echo esc_html($pocualrecb_post_title); ?></a>
                <a class="pocualrecb-template-arrow-link" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important;">
                    <?php echo $pocualrecb_arrow_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
            </li>
        <?php elseif ('minimal-outline' === $pocualrecb_template) : ?>
            <li class="postcue-also-read-content-block-listing-item pocualrecb-template-layout-minimal-outline" style="background-color: <?php echo esc_attr($pocualrecb_post_bg_color); ?> !important;">
                <div class="pocualrecb-template-content">
                    <a class="postcue-also-read-content-block-post-title" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important; font-size: <?php echo esc_attr($pocualrecb_post_title_font_size); ?> !important;"><?php echo esc_html($pocualrecb_post_title); ?></a>
                    <a class="pocualrecb-template-action" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important;"><?php echo esc_html__('Open article', 'postcue-also-read-content-block'); ?></a>
                </div>
                <?php echo $pocualrecb_image_markup($pocualrecb_post_thumbnail, $pocualrecb_post_link, $pocualrecb_post_title); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
            </li>
        <?php elseif ('sleek-card' === $pocualrecb_template) : ?>
            <li class="postcue-also-read-content-block-listing-item pocualrecb-template-layout-sleek-card" style="background-color: <?php echo esc_attr($pocualrecb_post_bg_color); ?> !important;">
                <?php echo $pocualrecb_image_markup($pocualrecb_post_thumbnail, $pocualrecb_post_link, $pocualrecb_post_title); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <div class="pocualrecb-template-content">
                    <span class="pocualrecb-template-pill"><?php echo esc_html($pocualrecb_block_title); ?></span>
                    <a class="postcue-also-read-content-block-post-title" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important; font-size: <?php echo esc_attr($pocualrecb_post_title_font_size); ?> !important;"><?php echo esc_html($pocualrecb_post_title); ?></a>
                    <a class="pocualrecb-template-action" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important;">
                        <?php echo $pocualrecb_arrow_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                        <span><?php echo esc_html__('Continue reading', 'postcue-also-read-content-block'); ?></span>
                    </a>
                </div>
            </li>
        <?php else : ?>
            <li class="postcue-also-read-content-block-listing-item pocualrecb-template-layout-compact" style="background-color: <?php echo esc_attr($pocualrecb_post_bg_color); ?> !important;">
                <?php echo $pocualrecb_image_markup($pocualrecb_post_thumbnail, $pocualrecb_post_link, $pocualrecb_post_title); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                <a class="postcue-also-read-content-block-post-title" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important; font-size: <?php echo esc_attr($pocualrecb_post_title_font_size); ?> !important;"><?php echo esc_html($pocualrecb_post_title); ?></a>
                <a class="pocualrecb-template-arrow-link" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url($pocualrecb_post_link); ?>" style="color: <?php echo esc_attr($pocualrecb_post_title_text_color); ?> !important;">
                    <?php echo $pocualrecb_arrow_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</div>
