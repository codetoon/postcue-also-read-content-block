<?php
add_action('admin_menu', function() {
    add_options_page(
        'Regur Also Read Post Settings',
        'Also Read Post',
        'manage_options',
        'regur-also-read-post',
        'regur_also_read_post_settings_page'
    );
});

function regur_also_read_post_settings_page() {
    if (isset($_POST['regur_defaults'])) {
        update_option('regur_also_read_post_defaults', $_POST['regur_defaults']);
        echo '<div class="updated"><p>Settings saved.</p></div>';
    }
    $defaults = get_option('regur_also_read_post_defaults', [
        'blockTitle' => 'Also Read',
        'textColor' => '#696969',
        'fontSize' => '18px',
        'postTitleTextColor' => '#ffffff',
        'postTitleFontSize' => '18px',
        'postBgColor' => '#06b7d3',
    ]);
    ?>
    <div class="wrap">
        <h1>Regur Also Read Post - Default Styles</h1>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th>Block Title</th>
                    <td><input name="regur_defaults[blockTitle]" value="<?php echo esc_attr($defaults['blockTitle']); ?>"></td>
                </tr>
                <tr>
                    <th>Block Title Color</th>
                    <td><input type="color" name="regur_defaults[textColor]" value="<?php echo esc_attr($defaults['textColor']); ?>"></td>
                </tr>
                <tr>
                    <th>Block Title Font Size</th>
                    <td><input name="regur_defaults[fontSize]" value="<?php echo esc_attr($defaults['fontSize']); ?>"></td>
                </tr>
                <tr>
                    <th>Post Title Color</th>
                    <td><input type="color" name="regur_defaults[postTitleTextColor]" value="<?php echo esc_attr($defaults['postTitleTextColor']); ?>"></td>
                </tr>
                <tr>
                    <th>Post Title Font Size</th>
                    <td><input name="regur_defaults[postTitleFontSize]" value="<?php echo esc_attr($defaults['postTitleFontSize']); ?>"></td>
                </tr>
                <tr>
                    <th>Post BG Color</th>
                    <td><input type="color" name="regur_defaults[postBgColor]" value="<?php echo esc_attr($defaults['postBgColor']); ?>"></td>
                </tr>
            </table>
            <input type="submit" class="button-primary" value="Save Changes">
        </form>
    </div>
    <?php
}
