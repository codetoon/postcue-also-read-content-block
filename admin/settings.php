<?php

if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}
require_once plugin_dir_path(__DIR__) . './includes/functions.php';
add_action('admin_menu', function () {
    if (current_user_can('edit_posts')) {
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
});

function pocualrecb_settings_page()
{
    $pocualrecb_input = null;

    // Avoid direct $_POST access in conditional
    if (! empty($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['pocualrecb_nonce']) && check_admin_referer('pocualrecb_save_settings', 'pocualrecb_nonce')) {
            // Safe to access now
            $pocualrecb_raw_input = filter_input(INPUT_POST, 'pocualrecb_defaults', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);

            if (is_array($pocualrecb_raw_input)) {
                $pocualrecb_input = wp_unslash($pocualrecb_raw_input); // unescape slashes from POST
            }
        }
    }

    if (is_array($pocualrecb_input)) {
        // Sanitize each field
        $pocualrecb_sanitized = [
            'blockTitle' => sanitize_text_field($pocualrecb_input['blockTitle'] ?? ''),
            'blockTitleTextColor' => sanitize_hex_color($pocualrecb_input['blockTitleTextColor'] ?? ''),
            'blockTitleFontSize' => sanitize_text_field($pocualrecb_input['blockTitleFontSize'] ?? ''),
            'postTitleTextColor' => sanitize_hex_color($pocualrecb_input['postTitleTextColor'] ?? ''),
            'postTitleFontSize' => sanitize_text_field($pocualrecb_input['postTitleFontSize'] ?? ''),
            'postBgColor' => sanitize_hex_color($pocualrecb_input['postBgColor'] ?? ''),
        ];

        update_option('pocualrecb_defaults', $pocualrecb_sanitized);
        echo '<div class="postcue-also-read-content-block-updated-message"><p>' . esc_html__('Settings saved.', 'postcue-also-read-content-block') . '</p></div>';
    }
    $pocualrecb_defaults = pocualrecb_get_global_defaults();
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
                    </table>
                    <input type="submit" class="postcue-also-read-content-block-button-primary" value="<?php echo esc_html__('Save Changes', 'postcue-also-read-content-block'); ?>">
                </form>
            </div>

            <div class="postcue-also-read-content-block-sidebar">
                <h2><?php echo esc_html__('About This Plugin', 'postcue-also-read-content-block'); ?></h2>
                <p>
                    <a href="https://postcue.regur.net/" target="_blank">
                        <?php echo esc_html__('Visit our Website', 'postcue-also-read-content-block'); ?>
                    </a>
                </p>

                <h2><?php echo esc_html__('Feedback', 'postcue-also-read-content-block'); ?></h2>
                <p>
                    <a href="https://postcue.regur.net/contact" class="postcue-also-read-content-block-button-secondary" target="_blank">
                        💡 <?php echo esc_html__('I have an idea', 'postcue-also-read-content-block'); ?>
                    </a>
                </p>
                <p>
                    <a href="https://postcue.regur.net/contact" class="postcue-also-read-content-block-button-secondary" target="_blank">
                        🛠️ <?php echo esc_html__('I need help', 'postcue-also-read-content-block'); ?>
                    </a>
                </p>
            </div>

        </div>
    </div>

<?php
}

add_action('admin_enqueue_scripts', 'pocualrecb_enqueue_admin_styles');

function pocualrecb_enqueue_admin_styles($pocualrecb_hook_suffix)
{
    if ('toplevel_page_postcue-also-read-content-block-settings' !== $pocualrecb_hook_suffix) {
        return;
    }

    $pocualrecb_style_file = 'admin/settings.css';
    $pocualrecb_style_path = plugin_dir_path(__DIR__) . $pocualrecb_style_file;
    $pocualrecb_style_url  = plugin_dir_url(__DIR__) . $pocualrecb_style_file;
    $pocualrecb_version    = file_exists($pocualrecb_style_path) ? (string) filemtime($pocualrecb_style_path) : '1.0.0';

    wp_enqueue_style('pocualrecb-admin-settings', $pocualrecb_style_url, [], $pocualrecb_version);
}
