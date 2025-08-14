<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
add_action('admin_menu', function() {
    add_options_page(
        'WP Also Read Settings',
        'WP Also Read',
        'manage_options',
        'rts-wp-also-read',
        'rts_wp_also_read_settings_page'
    );
});

function rts_wp_also_read_settings_page() {
    if (isset($_POST['rtswpar_defaults'])) {
        update_option('rts_wp_also_read_defaults', $_POST['rtswpar_defaults']);
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }
    $defaults = get_option('rts_wp_also_read_defaults', [
        'blockTitle' => 'Also Read',
        'textColor' => '#696969',
        'fontSize' => '18px',
        'postTitleTextColor' => '#ffffff',
        'postTitleFontSize' => '18px',
        'postBgColor' => '#06b7d3',
    ]);
    ?>
    <div class="wrap">
        <h1>WP Also Read - Default Styles</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th>Block Title</th>
                    <td><input name="rtswpar_defaults[blockTitle]" value="<?php echo esc_attr($defaults['blockTitle']); ?>"></td>
                </tr>
                <tr>
                    <th>Block Title Color</th>
                    <td><input type="color" name="rtswpar_defaults[textColor]" value="<?php echo esc_attr($defaults['textColor']); ?>"></td>
                </tr>
                <tr>
                    <th>Block Title Font Size</th>
                    <td><input name="rtswpar_defaults[fontSize]" value="<?php echo esc_attr($defaults['fontSize']); ?>"></td>
                </tr>
                <tr>
                    <th>Post Title Color</th>
                    <td><input type="color" name="rtswpar_defaults[postTitleTextColor]" value="<?php echo esc_attr($defaults['postTitleTextColor']); ?>"></td>
                </tr>
                <tr>
                    <th>Post Title Font Size</th>
                    <td><input name="rtswpar_defaults[postTitleFontSize]" value="<?php echo esc_attr($defaults['postTitleFontSize']); ?>"></td>
                </tr>
                <tr>
                    <th>Post BG Color</th>
                    <td><input type="color" name="rtswpar_defaults[postBgColor]" value="<?php echo esc_attr($defaults['postBgColor']); ?>"></td>
                </tr>
            </table>
            <input type="submit" class="button-primary" value="Save Changes">
        </form>
    </div>
    <?php
}
