<?php
/**
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
?>
<?php
$attributes = $attributes ?? [];

if(empty($selectedPost)){
	return '';
}

$title = esc_html($selectedPost['title'] ?? '');
$link = esc_url($selectedPost['link'] ?? '#');
$thumbnail = esc_url($selectedPost['thumbnail'] ?? '');
$selectedPost = $attributes['selectedPost'] ?? null;
$blockTitle = $attributes['blockTitle'] ?? '';
$textColor = $attributes['textColor'] ?? '';
$fontSize = $attributes['fontSize'] ?? '';
$postTitleTextColor = $attributes['postTitleTextColor'] ?? '';
$postBgColor = $attributes['postBgColor'] ?? '';
$postTitleFontSize = $attributes['postTitleFontSize'] ?? '';


?>
<div <?php echo get_block_wrapper_attributes(); ?>>
   <h2 class="display-posts-title" style="color: <?php echo esc_attr($textColor); ?> !important; font-size: <?php echo esc_attr($fontSize); ?> !important;"><?php echo esc_html($blockTitle); ?></h2>
     <ul class="display-posts-listing">
        <li class="listing-item" style="background-color: <?php echo esc_attr($postBgColor); ?> !important;">
        <?php if($thumbnail): ?>
            <a class="image" target="_blank" href="<?php echo $link; ?>">
                    <img width="150" height="150" src="<?php echo $thumbnail; ?>" class="attachment-thumbnail size-thumbnail wp-post-image" alt="<?php echo $title; ?>" />
            </a>
        <?php endif;?>
        <a class="title" target="_blank" href="<?php echo $link; ?>" style="color: <?php echo esc_attr($postTitleTextColor); ?> !important; background-color: <?php echo esc_attr($postBgColor); ?> !important; font-size: <?php echo esc_attr($postTitleFontSize); ?> !important;"><?php echo $title; ?></a>
        </li>
     </ul>
</div>
