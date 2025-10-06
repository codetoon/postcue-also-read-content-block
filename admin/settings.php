<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
require_once plugin_dir_path(__DIR__) . './includes/functions.php';
add_action('admin_menu', function() {
    if (current_user_can('edit_posts')) {
        add_menu_page(
            'PostCue Also Read Settings',
            'PostCue Also Read',
            'edit_posts',
            'postcue-also-read-settings',
            'postcue_alsoread_settings_page',
            plugin_dir_url(__DIR__) . 'images/icon.svg',
            80     
        );
    }
});

function postcue_alsoread_settings_page() {
    $input = null;

    // Avoid direct $_POST access in conditional
    if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        if ( isset( $_POST['postcue_alsoread_nonce'] ) && check_admin_referer( 'postcue_alsoread_save_settings', 'postcue_alsoread_nonce' ) ) {
            // Safe to access now
            $raw_input = filter_input( INPUT_POST, 'postcue_alsoread_defaults', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
            
            if ( is_array( $raw_input ) ) {
                $input = wp_unslash( $raw_input ); // unescape slashes from POST
            }
        }
    }

    if ( is_array( $input ) ) {
        // Sanitize each field
        $sanitized = [
            'blockTitle' => sanitize_text_field( $input['blockTitle'] ?? '' ),
            'blockTitleTextColor' => sanitize_hex_color( $input['blockTitleTextColor'] ?? '' ),
            'blockTitleFontSize' => sanitize_text_field( $input['blockTitleFontSize'] ?? '' ),
            'postTitleTextColor' => sanitize_hex_color( $input['postTitleTextColor'] ?? '' ),
            'postTitleFontSize' => sanitize_text_field( $input['postTitleFontSize'] ?? '' ),
            'postBgColor' => sanitize_hex_color( $input['postBgColor'] ?? '' ),
        ];

        update_option( 'postcue_alsoread_defaults', $sanitized );
        echo '<div class="updated"><p>' . esc_html__('Settings saved.', 'postcue-also-read-content-block') . '</p></div>';
    }
    $defaults = postcue_alsoread_get_global_defaults();
    ?>



    <div class="wrap">
        <h1><?php echo esc_html__('PostCue Also Read - Global Styles', 'postcue-also-read-content-block'); ?></h1>
        <p class="postcue-also-read-paragraph"><?php echo esc_html__('Use the settings below to customize the appearance of the "Also Read" block across your site. These global styles will be applied automatically unless you override them on individual posts.', 'postcue-also-read-content-block'); ?></p>

        <div class="postcue-also-read-container">
            <div class="postcue-also-read-main">
                <form method="post">
                    <?php wp_nonce_field('postcue_alsoread_save_settings', 'postcue_alsoread_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th><?php echo esc_html__('Block Title', 'postcue-also-read-content-block'); ?></th>
                            <td><input name="postcue_alsoread_defaults[blockTitle]" value="<?php echo esc_attr($defaults['blockTitle']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Block Title Color', 'postcue-also-read-content-block'); ?></th>
                            <td><input type="color" name="postcue_alsoread_defaults[blockTitleTextColor]" value="<?php echo esc_attr($defaults['blockTitleTextColor']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Block Title Font Size', 'postcue-also-read-content-block'); ?></th>
                            <td><input name="postcue_alsoread_defaults[blockTitleFontSize]" value="<?php echo esc_attr($defaults['blockTitleFontSize']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post Title Color', 'postcue-also-read-content-block'); ?></th>
                            <td><input type="color" name="postcue_alsoread_defaults[postTitleTextColor]" value="<?php echo esc_attr($defaults['postTitleTextColor']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post Title Font Size', 'postcue-also-read-content-block'); ?></th>
                            <td><input name="postcue_alsoread_defaults[postTitleFontSize]" value="<?php echo esc_attr($defaults['postTitleFontSize']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post BG Color', 'postcue-also-read-content-block'); ?></th>
                            <td><input type="color" name="postcue_alsoread_defaults[postBgColor]" value="<?php echo esc_attr($defaults['postBgColor']); ?>"></td>
                        </tr>
                    </table>
                    <input type="submit" class="button-primary" value="<?php echo esc_html__('Save Changes', 'postcue-also-read-content-block'); ?>">
                </form>
            </div>

            <div class="postcue-also-read-sidebar">
                <h2><?php echo esc_html__('About This Plugin', 'postcue-also-read-content-block'); ?></h2>
                <p>
                    <a href="https://postcue.regur.net/" target="_blank">
                        <?php echo esc_html__('Visit our Website', 'postcue-also-read-content-block'); ?>
                    </a>
                </p>

                <h2><?php echo esc_html__('Feedback', 'postcue-also-read-content-block'); ?></h2>
                <p>
                    <a href="https://postcue.regur.net/contact" class="button button-secondary" target="_blank">
                        💡 <?php echo esc_html__('I have an idea', 'postcue-also-read-content-block'); ?>
                    </a>
                </p>
                <p>
                    <a href="https://postcue.regur.net/contact" class="button button-secondary" target="_blank">
                        🛠️ <?php echo esc_html__('I need help', 'postcue-also-read-content-block'); ?>
                    </a>
                </p>
            </div>

        </div>
    </div>

    <?php
}

add_action( 'admin_print_styles', 'postalre_admin_inline_css' );

function postalre_admin_inline_css() {
    echo'
    <style>
    .postcue-also-read-container {
        display: flex;
        gap: 30px;
        align-items: flex-start;
    }
    .postcue-also-read-main {
        width: 35%;
    }
    .postcue-also-read-sidebar {
        width: 20%;
        background: #fff;
        border: 1px solid #ccd0d4;
        padding: 20px;
        border-radius: 10px;
    }
    .postcue-also-read-sidebar h2 {
        font-size: 16px;
        margin-top: 0;
    }
    .postcue-also-read-sidebar a.button {
        text-align: center;
    }
    p.postcue-also-read-paragraph{
        width:30%;
    }
    </style>';
}
