<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
if ( ! function_exists( 'pocualrecb_get_global_defaults' ) ) {
    function pocualrecb_get_global_defaults() {
        return get_option('pocualrecb_defaults', [
            'blockTitle' => 'Also Read',
            'blockTitleTextColor' => '#696969',
            'blockTitleFontSize' => '18px',
            'postTitleTextColor' => '#ffffff',
            'postTitleFontSize' => '18px',
            'postBgColor' => '#06b7d3',
        ]);
    }
}