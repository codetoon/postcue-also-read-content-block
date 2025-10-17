<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
require_once plugin_dir_path(__DIR__) . './includes/functions.php';
add_action('admin_menu', function() {
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

function pocualrecb_settings_page() {
    $input = null;

    // Avoid direct $_POST access in conditional
    if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        if ( isset( $_POST['pocualrecb_nonce'] ) && check_admin_referer( 'pocualrecb_save_settings', 'pocualrecb_nonce' ) ) {
            // Safe to access now
            $raw_input = filter_input( INPUT_POST, 'pocualrecb_defaults', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
            
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

        update_option( 'pocualrecb_defaults', $sanitized );
        echo '<div class="updated"><p>' . esc_html__('Settings saved.', 'postcue-also-read-content-block') . '</p></div>';
    }
    $defaults = pocualrecb_get_global_defaults();
    ?>



    <div class="postcue-also-read-content-block-wrap">
        <h1 class="postcue-also-read-content-block-heading"><?php echo esc_html__('PostCue Also Read Content Block - Global Styles', 'postcue-also-read-content-block'); ?></h1>
        <p class="postcue-also-read-content-block-paragraph"><?php echo esc_html__('Use the settings below to customize the appearance of the "Also Read" block across your site. These global styles will be applied automatically unless you override them on individual posts.', 'postcue-also-read-content-block'); ?></p>

        <div class="postcue-also-read-content-block-container">
            <div class="postcue-also-read-content-block-main">
                <form method="post">
                    <?php wp_nonce_field('pocualrecb_save_settings', 'pocualrecb_nonce'); ?>
                    <table class="form-table">
                        <tr>
                            <th><?php echo esc_html__('Block Title', 'postcue-also-read-content-block'); ?></th>
                            <td><input name="pocualrecb_defaults[blockTitle]" value="<?php echo esc_attr($defaults['blockTitle']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Block Title Color', 'postcue-also-read-content-block'); ?></th>
                            <td><input type="color" name="pocualrecb_defaults[blockTitleTextColor]" value="<?php echo esc_attr($defaults['blockTitleTextColor']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Block Title Font Size', 'postcue-also-read-content-block'); ?></th>
                            <td><input name="pocualrecb_defaults[blockTitleFontSize]" value="<?php echo esc_attr($defaults['blockTitleFontSize']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post Title Color', 'postcue-also-read-content-block'); ?></th>
                            <td><input type="color" name="pocualrecb_defaults[postTitleTextColor]" value="<?php echo esc_attr($defaults['postTitleTextColor']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post Title Font Size', 'postcue-also-read-content-block'); ?></th>
                            <td><input name="pocualrecb_defaults[postTitleFontSize]" value="<?php echo esc_attr($defaults['postTitleFontSize']); ?>"></td>
                        </tr>
                        <tr>
                            <th><?php echo esc_html__('Post BG Color', 'postcue-also-read-content-block'); ?></th>
                            <td><input type="color" name="pocualrecb_defaults[postBgColor]" value="<?php echo esc_attr($defaults['postBgColor']); ?>"></td>
                        </tr>
                    </table>
                    <input type="submit" class="button-primary" value="<?php echo esc_html__('Save Changes', 'postcue-also-read-content-block'); ?>">
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

add_action( 'admin_print_styles', 'pocualrecb_admin_inline_css' );

function pocualrecb_admin_inline_css() {
    echo'
    <style id="postcue-also-read-content-block-admin-inline-css">
    .postcue-also-read-content-block-wrap{
        .postcue-also-read-content-block-heading{
            line-height:1.2;
            margin-bottom:10px !important;
            font-size:26px !important;
            color:#23282d !important;
        }
        .postcue-also-read-content-block-container {
            display: flex;
            gap: 30px;
            align-items: flex-start;
        }
        .postcue-also-read-content-block-main {
            width: 35%;
        }
        .postcue-also-read-content-block-sidebar {
            width: 20%;
            background: #fff;
            border: 1px solid #ccd0d4;
            padding: 20px;
            border-radius: 10px;
        }
        .postcue-also-read-content-block-sidebar h2 {
            font-size: 16px;
            margin-top: 0;
        }
        .postcue-also-read-content-block-sidebar a.button {
            text-align: center;
        }
        p.postcue-also-read-content-block-paragraph{
            width:30%;
        }
        .postcue-also-read-content-block-container .button-primary{
            margin-top:12px !important;
        }
    }
    @media (max-width: 800px) {
    .postcue-also-read-content-block-wrap{
            .postcue-also-read-content-block-container {
                flex-direction: column;
                width: 100%;
            }
            .postcue-also-read-content-block-main, .postcue-also-read-content-block-sidebar {
                width: 80%;
            }
            p.postcue-also-read-content-block-paragraph{
                width:100%;
            }
        }
    }
    </style>';
}
