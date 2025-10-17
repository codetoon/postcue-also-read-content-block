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
    $pocualrecb_input = null;

    // Avoid direct $_POST access in conditional
    if ( ! empty( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' ) {
        if ( isset( $_POST['pocualrecb_nonce'] ) && check_admin_referer( 'pocualrecb_save_settings', 'pocualrecb_nonce' ) ) {
            // Safe to access now
            $pocualrecb_raw_input = filter_input( INPUT_POST, 'pocualrecb_defaults', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
            
            if ( is_array( $pocualrecb_raw_input ) ) {
                $pocualrecb_input = wp_unslash( $pocualrecb_raw_input ); // unescape slashes from POST
            }
        }
    }

    if ( is_array( $pocualrecb_input ) ) {
        // Sanitize each field
        $pocualrecb_sanitized = [
            'blockTitle' => sanitize_text_field( $pocualrecb_input['blockTitle'] ?? '' ),
            'blockTitleTextColor' => sanitize_hex_color( $pocualrecb_input['blockTitleTextColor'] ?? '' ),
            'blockTitleFontSize' => sanitize_text_field( $pocualrecb_input['blockTitleFontSize'] ?? '' ),
            'postTitleTextColor' => sanitize_hex_color( $pocualrecb_input['postTitleTextColor'] ?? '' ),
            'postTitleFontSize' => sanitize_text_field( $pocualrecb_input['postTitleFontSize'] ?? '' ),
            'postBgColor' => sanitize_hex_color( $pocualrecb_input['postBgColor'] ?? '' ),
        ];

        update_option( 'pocualrecb_defaults', $pocualrecb_sanitized );
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
        .postcue-also-read-content-block-container .postcue-also-read-content-block-button-primary{
            margin-top:12px !important;
                display: inline-block;
                text-decoration: none;
                font-size: 13px;
                line-height: 2.15384615;
                min-height: 30px;
                margin: 0;
                padding: 0 10px;
                cursor: pointer;
                border-width: 1px;
                border-style: solid;
                -webkit-appearance: none;
                border-radius: 3px;
                white-space: nowrap;
                box-sizing: border-box;
                text-align: center;
                background: #2271b1;
                border-color: #2271b1;
                color: #fff;
                text-decoration: none;
                text-shadow: none;
        }
        .postcue-also-read-content-block-form-table {
            border-collapse: collapse;
            margin-top: .5em;
            width: 100%;
            clear: both;
            font-size: 14px;
        }  
        .postcue-also-read-content-block-form-table th {
            vertical-align: top;
            text-align: left;
            padding: 20px 10px 20px 0;
            width: 200px;
            line-height: 1.3;
            color: #1d2327;
            text-shadow: none;
            font-size: 14px;
            font-weight: 600;
        } 
        .postcue-also-read-content-block-form-table td {
            margin-bottom: 9px;
            padding: 15px 10px;
            line-height: 1.3;
            vertical-align: middle;
        }
        .postcue-also-read-content-block-form-table input{
            font-size: 14px;
        }
        .postcue-also-read-content-block-button-secondary{
            color: #2271b1;
            border-color: #2271b1;
            background: #f6f7f7;
            vertical-align: top;
            display: inline-block;
            text-decoration: none;
            font-size: 13px;
            line-height: 2.15384615;
            min-height: 30px;
            margin: 0;
            padding: 0 10px;
            cursor: pointer;
            border-width: 1px;
            border-style: solid;
            -webkit-appearance: none;
            border-radius: 3px;
            white-space: nowrap;
            box-sizing: border-box;
        }
    }
    .postcue-also-read-content-block-updated-message{
        background: #fff;
        border: 1px solid #c3c4c7;
        border-left-width: 4px;
        box-shadow: 0 1px 1px rgba(0,0,0,.04);
        margin: 5px 2px 2px;
        padding: 1px 12px;
        border-left-color: #00a32a;
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
