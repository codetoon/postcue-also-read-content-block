<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! function_exists( 'rtswpar_get_global_defaults' ) ) {
    function rtswpar_get_global_defaults() {
        return get_option('rts_wp_also_read_defaults', [
            'blockTitle' => 'Also Read',
            'blockTitleTextColor' => '#696969',
            'blockTitleFontSize' => '18px',
            'postTitleTextColor' => '#ffffff',
            'postTitleFontSize' => '18px',
            'postBgColor' => '#06b7d3',
        ]);
    }
}