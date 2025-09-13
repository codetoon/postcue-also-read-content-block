<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
require_once plugin_dir_path(__DIR__) . './includes/functions.php';
add_action('admin_menu', function() {
    if (current_user_can('edit_posts')) {
        add_menu_page(
            'WP Also Read Settings',
            'WP Also Read',
            'edit_posts',
            'rts-wp-also-read-settings',
            'rtswpar_settings_page',
            'dashicons-admin-generic',
            80     
        );
    }
});

function rtswpar_settings_page() {
    $input = null;

    // Avoid direct $_POST access in conditional
    if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        if ( isset( $_POST['rtswpar_nonce'] ) && check_admin_referer( 'rtswpar_save_settings', 'rtswpar_nonce' ) ) {
            // Safe to access now
            $raw_input = filter_input( INPUT_POST, 'rtswpar_defaults', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
            
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

        update_option( 'rts_wp_also_read_defaults', $sanitized );
        echo '<div class="updated"><p>' . esc_html__('Settings saved.', 'rts-wp-also-read') . '</p></div>';
    }
    $defaults = rtswpar_get_global_defaults();
    ?>

    <style>
        .rtswpar-container {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }
        .rtswpar-main {
            width: 35%;
        }
        .rtswpar-sidebar {
            width: 30%;
            background: #fff;
            border: 1px solid #ccd0d4;
            padding: 20px;
            border-radius: 10px;
        }
        .rtswpar-sidebar h2 {
            font-size: 16px;
            margin-top: 0;
        }
        .rtswpar-sidebar a.button {
            text-align: center;
        }
    </style>

    <div class="wrap">
        <h1><?php echo esc_html__('WP Also Read - Global Styles', 'rts-wp-also-read'); ?></h1>
        <p style="width:30%"><?php echo esc_html__('Use the settings below to customize the appearance of the "Also Read" block across your site. These global styles will be applied automatically unless you override them on individual posts.', 'rts-wp-also-read'); ?></p>

        <div class="rtswpar-container">
            <div class="rtswpar-main">
                <form method="post">
                    <?php wp_nonce_field('rtswpar_save_settings', 'rtswpar_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th><?php echo esc_html__('Block Title', 'rts-wp-also-read'); ?></th>
                            <td><input name="rtswpar_defaults[blockTitle]" value="<?php echo esc_attr($defaults['blockTitle']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Block Title Color', 'rts-wp-also-read'); ?></th>
                            <td><input type="color" name="rtswpar_defaults[blockTitleTextColor]" value="<?php echo esc_attr($defaults['blockTitleTextColor']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Block Title Font Size', 'rts-wp-also-read'); ?></th>
                            <td><input name="rtswpar_defaults[blockTitleFontSize]" value="<?php echo esc_attr($defaults['blockTitleFontSize']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post Title Color', 'rts-wp-also-read'); ?></th>
                            <td><input type="color" name="rtswpar_defaults[postTitleTextColor]" value="<?php echo esc_attr($defaults['postTitleTextColor']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post Title Font Size', 'rts-wp-also-read'); ?></th>
                            <td><input name="rtswpar_defaults[postTitleFontSize]" value="<?php echo esc_attr($defaults['postTitleFontSize']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post BG Color', 'rts-wp-also-read'); ?></th>
                            <td><input type="color" name="rtswpar_defaults[postBgColor]" value="<?php echo esc_attr($defaults['postBgColor']); ?>"></td>
                        </tr>
                    </table>
                    <input type="submit" class="button-primary" value="<?php echo esc_html__('Save Changes', 'rts-wp-also-read'); ?>">
                </form>
            </div>

            <div class="rtswpar-sidebar">
                <h2>About This Plugin</h2>
                <p><a href="https://wpalsoread.com" target="_blank">Visit our Website</a></p>

                <h2>Feedback</h2>
                <p><a href="https://wpalsoread.com/contact" class="button button-secondary" target="_blank">💡 I have an idea</a></p>
                <p><a href="https://wpalsoread.com/contact" class="button button-secondary" target="_blank">🛠️ I need help</a></p>
            </div>
        </div>
    </div>

    <?php
}


