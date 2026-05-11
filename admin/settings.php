<?php

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

require_once plugin_dir_path(__DIR__) . './includes/functions.php';

/**
 * Register plugin settings page.
 *
 * @return void
 */
function pocualrecb_add_settings_menu()
{
    if (! current_user_can('edit_posts')) {
        return;
    }

    add_menu_page(
        'PostCue Also Read Content Block Settings',
        'PostCue Also Read Content Block',
        'edit_posts',
        'postcue-also-read-content-block-settings',
        'pocualrecb_settings_page',
        plugin_dir_url(__DIR__) . 'images/icon.svg',
        80
    );
}

add_action('admin_menu', 'pocualrecb_add_settings_menu');

/**
 * Enqueue admin assets for plugin settings page.
 *
 * @param string $pocualrecb_hook_suffix Current admin page hook suffix.
 * @return void
 */
function pocualrecb_enqueue_admin_assets($pocualrecb_hook_suffix)
{
    if ('toplevel_page_postcue-also-read-content-block-settings' !== $pocualrecb_hook_suffix) {
        return;
    }

    $pocualrecb_style_path = plugin_dir_path(__FILE__) . 'assets/settings.css';
    $pocualrecb_script_path = plugin_dir_path(__FILE__) . 'assets/settings.js';

    wp_enqueue_style(
        'pocualrecb-admin-settings',
        plugin_dir_url(__FILE__) . 'assets/settings.css',
        [],
        file_exists($pocualrecb_style_path) ? (string) filemtime($pocualrecb_style_path) : '1.1.0'
    );

    wp_enqueue_script(
        'pocualrecb-admin-settings',
        plugin_dir_url(__FILE__) . 'assets/settings.js',
        [],
        file_exists($pocualrecb_script_path) ? (string) filemtime($pocualrecb_script_path) : '1.1.0',
        true
    );
}

add_action('admin_enqueue_scripts', 'pocualrecb_enqueue_admin_assets');

/**
 * Render plugin settings page.
 *
 * @return void
 */
function pocualrecb_settings_page()
{
    $pocualrecb_input = null;
    $pocualrecb_templates = pocualrecb_get_template_choices();

    if (! empty($_SERVER['REQUEST_METHOD']) && 'POST' === $_SERVER['REQUEST_METHOD']) {
        if (isset($_POST['pocualrecb_nonce']) && check_admin_referer('pocualrecb_save_settings', 'pocualrecb_nonce')) {
            $pocualrecb_raw_input = filter_input(INPUT_POST, 'pocualrecb_defaults', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            if (is_array($pocualrecb_raw_input)) {
                $pocualrecb_input = wp_unslash($pocualrecb_raw_input);
            }
        }
    }

    if (is_array($pocualrecb_input)) {
        $pocualrecb_selected_template = pocualrecb_sanitize_template($pocualrecb_input['template'] ?? 'default');
        $pocualrecb_template_styles = pocualrecb_get_sanitized_template_styles(
            $pocualrecb_input['templateStyles'] ?? [],
            $pocualrecb_input,
            $pocualrecb_selected_template
        );
        $pocualrecb_selected_template_style = $pocualrecb_template_styles[ $pocualrecb_selected_template ] ?? pocualrecb_get_style_field_defaults($pocualrecb_selected_template);
        $pocualrecb_sanitized = array_merge(
            $pocualrecb_selected_template_style,
            [
                'template' => $pocualrecb_selected_template,
                'templateStyles' => $pocualrecb_template_styles,
            ]
        );

        update_option('pocualrecb_defaults', $pocualrecb_sanitized);
        echo '<div class="postcue-also-read-content-block-updated-message"><p>' . esc_html__('Settings saved.', 'postcue-also-read-content-block') . '</p></div>';
    }

    $pocualrecb_defaults = pocualrecb_get_global_defaults();
    $pocualrecb_selected_template = $pocualrecb_defaults['template'] ?? 'default';
    $pocualrecb_template_styles = $pocualrecb_defaults['templateStyles'] ?? [];
    $pocualrecb_template_styles = is_array($pocualrecb_template_styles) ? $pocualrecb_template_styles : [];
    $pocualrecb_template_default_styles = pocualrecb_get_template_style_defaults();
    $pocualrecb_style_fields = [
        'blockTitle' => [
            'label' => __('Block Title', 'postcue-also-read-content-block'),
            'type' => 'text',
        ],
        'blockTitleTextColor' => [
            'label' => __('Block Title Color', 'postcue-also-read-content-block'),
            'type' => 'color',
        ],
        'blockTitleFontSize' => [
            'label' => __('Block Title Font Size', 'postcue-also-read-content-block'),
            'type' => 'text',
        ],
        'postTitleTextColor' => [
            'label' => __('Post Title Color', 'postcue-also-read-content-block'),
            'type' => 'color',
        ],
        'postTitleFontSize' => [
            'label' => __('Post Title Font Size', 'postcue-also-read-content-block'),
            'type' => 'text',
        ],
        'postBgColor' => [
            'label' => __('Post BG Color', 'postcue-also-read-content-block'),
            'type' => 'color',
        ],
    ];
    ?>

    <div class="postcue-also-read-content-block-wrap">
        <h1 class="postcue-also-read-content-block-heading"><?php echo esc_html__('PostCue Also Read Content Block - Global Styles', 'postcue-also-read-content-block'); ?></h1>
        <p class="postcue-also-read-content-block-paragraph"><?php echo esc_html__('Use the settings below to customize the appearance of the "Also Read" block across your site. These global styles will be applied automatically unless you override them on individual posts.', 'postcue-also-read-content-block'); ?></p>

        <div class="postcue-also-read-content-block-container">
            <div class="postcue-also-read-content-block-main">
                <form method="post">
                    <?php wp_nonce_field('pocualrecb_save_settings', 'pocualrecb_nonce'); ?>
                    <table class="postcue-also-read-content-block-form-table">
                        <tr>
                            <th><?php echo esc_html__('Template', 'postcue-also-read-content-block'); ?></th>
                            <td>
                                <select name="pocualrecb_defaults[template]">
                                    <?php foreach ($pocualrecb_templates as $pocualrecb_template_key => $pocualrecb_template_label) : ?>
                                        <option value="<?php echo esc_attr($pocualrecb_template_key); ?>" <?php selected($pocualrecb_defaults['template'] ?? 'default', $pocualrecb_template_key); ?>>
                                            <?php echo esc_html($pocualrecb_template_label); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <p class="description"><?php echo esc_html__('Changing this template updates all Also Read blocks site-wide.', 'postcue-also-read-content-block'); ?></p>
                            </td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Template Styles', 'postcue-also-read-content-block'); ?></th>
                            <td>
                                <p class="description"><?php echo esc_html__('Each template has its own style values. Switch the template above to edit that template settings.', 'postcue-also-read-content-block'); ?></p>
                                <div class="pocualrecb-template-settings-wrap">
                                    <?php foreach ($pocualrecb_templates as $pocualrecb_template_key => $pocualrecb_template_label) : ?>
                                        <?php
                                        $pocualrecb_active_template_panel = $pocualrecb_selected_template === $pocualrecb_template_key ? 'is-active' : '';
                                        $pocualrecb_template_default_style = $pocualrecb_template_default_styles[ $pocualrecb_template_key ] ?? pocualrecb_get_style_field_defaults($pocualrecb_template_key);
                                        $pocualrecb_template_style = $pocualrecb_template_styles[ $pocualrecb_template_key ] ?? [];
                                        $pocualrecb_template_style = pocualrecb_sanitize_style_settings(
                                            $pocualrecb_template_style,
                                            $pocualrecb_template_default_style,
                                            $pocualrecb_template_key
                                        );
                                        ?>
                                        <div class="pocualrecb-template-settings-panel <?php echo esc_attr($pocualrecb_active_template_panel); ?>" data-template="<?php echo esc_attr($pocualrecb_template_key); ?>">
                                            <strong class="pocualrecb-template-settings-title">
                                                <?php
                                                printf(
                                                    /* translators: %s is template label. */
                                                    esc_html__('%s style settings', 'postcue-also-read-content-block'),
                                                    esc_html($pocualrecb_template_label)
                                                );
                                                ?>
                                            </strong>
                                            <div class="pocualrecb-template-settings-grid">
                                                <?php foreach ($pocualrecb_style_fields as $pocualrecb_style_field_key => $pocualrecb_style_field_data) : ?>
                                                    <label class="pocualrecb-template-settings-field">
                                                        <span><?php echo esc_html($pocualrecb_style_field_data['label']); ?></span>
                                                        <input
                                                            class="pocualrecb-template-setting-input"
                                                            data-field="<?php echo esc_attr($pocualrecb_style_field_key); ?>"
                                                            data-default-value="<?php echo esc_attr($pocualrecb_template_default_style[ $pocualrecb_style_field_key ] ?? ''); ?>"
                                                            type="<?php echo esc_attr($pocualrecb_style_field_data['type']); ?>"
                                                            name="pocualrecb_defaults[templateStyles][<?php echo esc_attr($pocualrecb_template_key); ?>][<?php echo esc_attr($pocualrecb_style_field_key); ?>]"
                                                            value="<?php echo esc_attr($pocualrecb_template_style[ $pocualrecb_style_field_key ] ?? ''); ?>"
                                                        >
                                                    </label>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                        </tr>
                    </table>

                    <div class="pocualrecb-template-preview-wrap">
                        <h2><?php echo esc_html__('Template Preview', 'postcue-also-read-content-block'); ?></h2>
                        <p class="description"><?php echo esc_html__('Preview updates based on the selected template and style values above. Click a preview card to switch templates.', 'postcue-also-read-content-block'); ?></p>
                        <div class="pocualrecb-template-preview-grid">
                            <?php foreach ($pocualrecb_templates as $pocualrecb_template_key => $pocualrecb_template_label) : ?>
                                <?php
                                $pocualrecb_active_template_class = $pocualrecb_selected_template === $pocualrecb_template_key ? 'is-active' : '';
                                $pocualrecb_template_default_style = $pocualrecb_template_default_styles[ $pocualrecb_template_key ] ?? pocualrecb_get_style_field_defaults($pocualrecb_template_key);
                                $pocualrecb_template_style = $pocualrecb_template_styles[ $pocualrecb_template_key ] ?? [];
                                $pocualrecb_template_style = pocualrecb_sanitize_style_settings(
                                    $pocualrecb_template_style,
                                    $pocualrecb_template_default_style,
                                    $pocualrecb_template_key
                                );
                                ?>
                                <div class="pocualrecb-template-preview-card <?php echo esc_attr($pocualrecb_active_template_class); ?> pocualrecb-template-preview-<?php echo esc_attr($pocualrecb_template_key); ?>" data-template="<?php echo esc_attr($pocualrecb_template_key); ?>" role="button" tabindex="0" aria-label="<?php echo esc_attr__('Switch template preview', 'postcue-also-read-content-block'); ?>">
                                    <strong class="pocualrecb-template-preview-name"><?php echo esc_html($pocualrecb_template_label); ?></strong>
                                    <div class="pocualrecb-template-preview-block">
                                        <div class="pocualrecb-template-preview-title" data-preview-role="block-title" style="color: <?php echo esc_attr($pocualrecb_template_style['blockTitleTextColor']); ?>; font-size: <?php echo esc_attr($pocualrecb_template_style['blockTitleFontSize']); ?>;">
                                            <?php echo esc_html($pocualrecb_template_style['blockTitle']); ?>
                                        </div>
                                        <?php if ('soft-card' === $pocualrecb_template_key) : ?>
                                            <div class="pocualrecb-template-preview-item pocualrecb-template-preview-item-soft-card" data-preview-role="post-item" style="background-color: <?php echo esc_attr($pocualrecb_template_style['postBgColor']); ?>;">
                                                <div class="pocualrecb-template-preview-image" data-preview-role="post-image"></div>
                                                <div class="pocualrecb-template-preview-content">
                                                    <div class="pocualrecb-template-preview-post-title" data-preview-role="post-title" style="color: <?php echo esc_attr($pocualrecb_template_style['postTitleTextColor']); ?>; font-size: <?php echo esc_attr($pocualrecb_template_style['postTitleFontSize']); ?>;">
                                                        <?php echo esc_html__('Sample post title', 'postcue-also-read-content-block'); ?>
                                                    </div>
                                                    <div class="pocualrecb-template-preview-action" style="color: <?php echo esc_attr($pocualrecb_template_style['postTitleTextColor']); ?> !important;"><?php echo esc_html__('Read post →', 'postcue-also-read-content-block'); ?></div>
                                                </div>
                                            </div>
                                        <?php elseif ('accent-strip' === $pocualrecb_template_key) : ?>
                                            <div class="pocualrecb-template-preview-item pocualrecb-template-preview-item-accent-strip" data-preview-role="post-item" style="background-color: <?php echo esc_attr($pocualrecb_template_style['postBgColor']); ?>;">
                                                <div class="pocualrecb-template-preview-image" data-preview-role="post-image"></div>
                                                <div class="pocualrecb-template-preview-post-title" data-preview-role="post-title" style="color: <?php echo esc_attr($pocualrecb_template_style['postTitleTextColor']); ?>; font-size: <?php echo esc_attr($pocualrecb_template_style['postTitleFontSize']); ?>;">
                                                    <?php echo esc_html__('Sample post title', 'postcue-also-read-content-block'); ?>
                                                </div>
                                                <div class="pocualrecb-template-preview-arrow" data-preview-role="post-arrow" style="color: <?php echo esc_attr($pocualrecb_template_style['postTitleTextColor']); ?> !important;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#000" width="20" height="20" aria-hidden="true" focusable="false">><path d="M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"></path></svg></div>
                                            </div>
                                        <?php elseif ('minimal-outline' === $pocualrecb_template_key) : ?>
                                            <div class="pocualrecb-template-preview-item pocualrecb-template-preview-item-minimal-outline" data-preview-role="post-item" style="background-color: <?php echo esc_attr($pocualrecb_template_style['postBgColor']); ?>;">
                                                <div class="pocualrecb-template-preview-content">
                                                    <div class="pocualrecb-template-preview-post-title" data-preview-role="post-title" style="color: <?php echo esc_attr($pocualrecb_template_style['postTitleTextColor']); ?>; font-size: <?php echo esc_attr($pocualrecb_template_style['postTitleFontSize']); ?>;">
                                                        <?php echo esc_html__('Sample post title', 'postcue-also-read-content-block'); ?>
                                                    </div>
                                                    <div class="pocualrecb-template-preview-action" style="color: <?php echo esc_attr($pocualrecb_template_style['postTitleTextColor']); ?> !important;"><?php echo esc_html__('Open article', 'postcue-also-read-content-block'); ?></div>
                                                </div>
                                                <div class="pocualrecb-template-preview-image" data-preview-role="post-image"></div>
                                            </div>
                                        <?php elseif ('sleek-card' === $pocualrecb_template_key) : ?>
                                            <div class="pocualrecb-template-preview-item pocualrecb-template-preview-item-sleek-card" data-preview-role="post-item" style="background-color: <?php echo esc_attr($pocualrecb_template_style['postBgColor']); ?>;">
                                                <div class="pocualrecb-template-preview-image" data-preview-role="post-image"></div>
                                                <div class="pocualrecb-template-preview-content">
                                                    <div class="pocualrecb-template-preview-pill"><?php echo esc_html($pocualrecb_template_style['blockTitle']); ?></div>
                                                    <div class="pocualrecb-template-preview-post-title" data-preview-role="post-title" style="color: <?php echo esc_attr($pocualrecb_template_style['postTitleTextColor']); ?>; font-size: <?php echo esc_attr($pocualrecb_template_style['postTitleFontSize']); ?>;">
                                                        <?php echo esc_html__('Sample post title', 'postcue-also-read-content-block'); ?>
                                                    </div>
                                                    <div class="pocualrecb-template-preview-action" style="color: <?php echo esc_attr($pocualrecb_template_style['postTitleTextColor']); ?> !important;"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="#000" width="20" height="20" aria-hidden="true" focusable="false">><path d="M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"></path></svg> <?php echo esc_html__('Continue reading', 'postcue-also-read-content-block'); ?></div>
                                                </div>
                                            </div>
                                        <?php elseif ('compact' === $pocualrecb_template_key) : ?>
                                            <div class="pocualrecb-template-preview-item pocualrecb-template-preview-item-compact" data-preview-role="post-item" style="background-color: <?php echo esc_attr($pocualrecb_template_style['postBgColor']); ?>;">
                                                <div class="pocualrecb-template-preview-image" data-preview-role="post-image"></div>
                                                <div class="pocualrecb-template-preview-post-title" data-preview-role="post-title" style="color: <?php echo esc_attr($pocualrecb_template_style['postTitleTextColor']); ?>; font-size: <?php echo esc_attr($pocualrecb_template_style['postTitleFontSize']); ?>;">
                                                    <?php echo esc_html__('This is a sample post title', 'postcue-also-read-content-block'); ?>
                                                    <div class="pocualrecb-template-preview-action" style="color: <?php echo esc_attr($pocualrecb_template_style['postTitleTextColor']); ?> !important;"><?php echo esc_html__('Read More →', 'postcue-also-read-content-block'); ?></div>
                                                </div>
                                            </div>
                                        <?php else : ?>
                                            <div class="pocualrecb-template-preview-item" data-preview-role="post-item" style="background-color: <?php echo esc_attr($pocualrecb_template_style['postBgColor']); ?>;">
                                                <div class="pocualrecb-template-preview-image" data-preview-role="post-image"></div>
                                                <div class="pocualrecb-template-preview-post-title" data-preview-role="post-title" style="color: <?php echo esc_attr($pocualrecb_template_style['postTitleTextColor']); ?>; font-size: <?php echo esc_attr($pocualrecb_template_style['postTitleFontSize']); ?>;">
                                                    <?php echo esc_html__('Sample post title', 'postcue-also-read-content-block'); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <input type="submit" class="postcue-also-read-content-block-button-primary" value="<?php echo esc_html__('Save Changes', 'postcue-also-read-content-block'); ?>">
                </form>
            </div>

            <div class="postcue-also-read-content-block-sidebar">
                <h2><?php echo esc_html__('About This Plugin', 'postcue-also-read-content-block'); ?></h2>
                <p>
                    <a href="https://postcue.regur.net/" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html__('Visit our Website', 'postcue-also-read-content-block'); ?>
                    </a>
                </p>

                <h2><?php echo esc_html__('Feedback', 'postcue-also-read-content-block'); ?></h2>
                <p>
                    <a href="https://postcue.regur.net/contact" class="postcue-also-read-content-block-button-secondary" target="_blank" rel="noopener noreferrer">
                         💡 <?php echo esc_html__('I have an idea', 'postcue-also-read-content-block'); ?>
                    </a>
                </p>
                <p>
                    <a href="https://postcue.regur.net/contact" class="postcue-also-read-content-block-button-secondary" target="_blank" rel="noopener noreferrer">
                        🛠️ <?php echo esc_html__('I need help', 'postcue-also-read-content-block'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
    <?php
}
