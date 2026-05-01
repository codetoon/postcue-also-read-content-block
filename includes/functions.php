<?php
if (! defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

if (! function_exists('pocualrecb_get_template_choices')) {
    /**
     * Returns the list of available global templates.
     *
     * @return array<string, string>
     */
    function pocualrecb_get_template_choices()
    {
        return [
            'default' => __('Default', 'postcue-also-read-content-block'),
            'soft-card' => __('Soft Card', 'postcue-also-read-content-block'),
            'accent-strip' => __('Accent Strip', 'postcue-also-read-content-block'),
            'minimal-outline' => __('Minimal Outline', 'postcue-also-read-content-block'),
            'split-highlight' => __('Split Highlight', 'postcue-also-read-content-block'),
            'compact' => __('Compact', 'postcue-also-read-content-block'),
        ];
    }
}

if (! function_exists('pocualrecb_sanitize_template')) {
    /**
     * Sanitizes and validates template slug.
     *
     * @param string $pocualrecb_template Template slug.
     * @return string
     */
    function pocualrecb_sanitize_template($pocualrecb_template)
    {
        $pocualrecb_template = sanitize_key((string) $pocualrecb_template);
        $pocualrecb_templates = pocualrecb_get_template_choices();

        return isset($pocualrecb_templates[ $pocualrecb_template ]) ? $pocualrecb_template : 'default';
    }
}

if (! function_exists('pocualrecb_get_default_settings')) {
    /**
     * Returns plugin default settings.
     *
     * @return array<string, string>
     */
    function pocualrecb_get_default_settings()
    {
        return [
            'blockTitle' => 'Also Read',
            'blockTitleTextColor' => '#696969',
            'blockTitleFontSize' => '18px',
            'postTitleTextColor' => '#ffffff',
            'postTitleFontSize' => '18px',
            'postBgColor' => '#06b7d3',
            'template' => 'default',
        ];
    }
}

if (! function_exists('pocualrecb_get_global_defaults')) {
    /**
     * Returns merged global defaults with backward compatibility.
     *
     * @return array<string, string>
     */
    function pocualrecb_get_global_defaults()
    {
        $pocualrecb_saved = get_option('pocualrecb_defaults', []);
        $pocualrecb_saved = is_array($pocualrecb_saved) ? $pocualrecb_saved : [];

        $pocualrecb_defaults = wp_parse_args($pocualrecb_saved, pocualrecb_get_default_settings());
        $pocualrecb_defaults['template'] = pocualrecb_sanitize_template($pocualrecb_defaults['template'] ?? 'default');

        return $pocualrecb_defaults;
    }
}
