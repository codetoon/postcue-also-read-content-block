<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! function_exists( 'postcue_alsoread_get_global_defaults' ) ) {
    function postcue_alsoread_get_global_defaults() {
        return get_option('postcue_alsoread_defaults', [
            'blockTitle' => 'Also Read',
            'blockTitleTextColor' => '#696969',
            'blockTitleFontSize' => '18px',
            'postTitleTextColor' => '#ffffff',
            'postTitleFontSize' => '18px',
            'postBgColor' => '#06b7d3',
        ]);
    }
}