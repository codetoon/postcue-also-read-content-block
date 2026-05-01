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
        $pocualrecb_sanitized = [
            'blockTitle' => sanitize_text_field($pocualrecb_input['blockTitle'] ?? ''),
            'blockTitleTextColor' => sanitize_hex_color($pocualrecb_input['blockTitleTextColor'] ?? ''),
            'blockTitleFontSize' => sanitize_text_field($pocualrecb_input['blockTitleFontSize'] ?? ''),
            'postTitleTextColor' => sanitize_hex_color($pocualrecb_input['postTitleTextColor'] ?? ''),
            'postTitleFontSize' => sanitize_text_field($pocualrecb_input['postTitleFontSize'] ?? ''),
            'postBgColor' => sanitize_hex_color($pocualrecb_input['postBgColor'] ?? ''),
            'template' => pocualrecb_sanitize_template($pocualrecb_input['template'] ?? 'default'),
        ];

        update_option('pocualrecb_defaults', $pocualrecb_sanitized);
        echo '<div class="postcue-also-read-content-block-updated-message"><p>' . esc_html__('Settings saved.', 'postcue-also-read-content-block') . '</p></div>';
    }

    $pocualrecb_defaults = pocualrecb_get_global_defaults();
    $pocualrecb_selected_template = $pocualrecb_defaults['template'] ?? 'default';
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
                            <th><?php echo esc_html__('Block Title', 'postcue-also-read-content-block'); ?></th>
                            <td><input name="pocualrecb_defaults[blockTitle]" value="<?php echo esc_attr($pocualrecb_defaults['blockTitle']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Block Title Color', 'postcue-also-read-content-block'); ?></th>
                            <td><input type="color" name="pocualrecb_defaults[blockTitleTextColor]" value="<?php echo esc_attr($pocualrecb_defaults['blockTitleTextColor']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Block Title Font Size', 'postcue-also-read-content-block'); ?></th>
                            <td><input name="pocualrecb_defaults[blockTitleFontSize]" value="<?php echo esc_attr($pocualrecb_defaults['blockTitleFontSize']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post Title Color', 'postcue-also-read-content-block'); ?></th>
                            <td><input type="color" name="pocualrecb_defaults[postTitleTextColor]" value="<?php echo esc_attr($pocualrecb_defaults['postTitleTextColor']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post Title Font Size', 'postcue-also-read-content-block'); ?></th>
                            <td><input name="pocualrecb_defaults[postTitleFontSize]" value="<?php echo esc_attr($pocualrecb_defaults['postTitleFontSize']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post BG Color', 'postcue-also-read-content-block'); ?></th>
                            <td><input type="color" name="pocualrecb_defaults[postBgColor]" value="<?php echo esc_attr($pocualrecb_defaults['postBgColor']); ?>"></td>
                        </tr>
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
                    </table>

                    <div class="pocualrecb-template-preview-wrap">
                        <h2><?php echo esc_html__('Template Preview', 'postcue-also-read-content-block'); ?></h2>
                        <p class="description"><?php echo esc_html__('Click the preview card to switch to the next template.', 'postcue-also-read-content-block'); ?></p>
                        <div class="pocualrecb-template-preview-grid">
                            <?php foreach ($pocualrecb_templates as $pocualrecb_template_key => $pocualrecb_template_label) : ?>
                                <?php
                                $pocualrecb_active_template_class = $pocualrecb_selected_template === $pocualrecb_template_key ? 'is-active' : '';
                                ?>
                                <div class="pocualrecb-template-preview-card <?php echo esc_attr($pocualrecb_active_template_class); ?> pocualrecb-template-preview-<?php echo esc_attr($pocualrecb_template_key); ?>" data-template="<?php echo esc_attr($pocualrecb_template_key); ?>" role="button" tabindex="0" aria-label="<?php echo esc_attr__('Switch template preview', 'postcue-also-read-content-block'); ?>">
                                    <strong class="pocualrecb-template-preview-name"><?php echo esc_html($pocualrecb_template_label); ?></strong>
                                    <div class="pocualrecb-template-preview-block">
                                        <div class="pocualrecb-template-preview-title"><?php echo esc_html__('Also Read', 'postcue-also-read-content-block'); ?></div>
                                        <div class="pocualrecb-template-preview-item">
                                            <div class="pocualrecb-template-preview-image"></div>
                                            <div class="pocualrecb-template-preview-text"><?php echo esc_html__('Sample post title', 'postcue-also-read-content-block'); ?></div>
                                        </div>
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
                        <?php echo esc_html__('I have an idea', 'postcue-also-read-content-block'); ?>
                    </a>
                </p>
                <p>
                    <a href="https://postcue.regur.net/contact" class="postcue-also-read-content-block-button-secondary" target="_blank" rel="noopener noreferrer">
                        <?php echo esc_html__('I need help', 'postcue-also-read-content-block'); ?>
                    </a>
                </p>
            </div>
        </div>
    </div>
    <?php
}
