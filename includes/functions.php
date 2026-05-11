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
            'sleek-card' => __('Sleek Card', 'postcue-also-read-content-block'),
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

if (! function_exists('pocualrecb_get_template_style_defaults')) {
    /**
     * Returns default style values for every template.
     *
     * @return array<string, array<string, string>>
     */
    function pocualrecb_get_template_style_defaults()
    {
        return [
            'default' => [
                'blockTitle' => 'Also Read',
                'blockTitleTextColor' => '#696969',
                'blockTitleFontSize' => '18px',
                'postTitleTextColor' => '#ffffff',
                'postTitleFontSize' => '18px',
                'postBgColor' => '#06b7d3',
            ],
            'soft-card' => [
                'blockTitle' => 'Must Read',
                'blockTitleTextColor' => '#1f2937',
                'blockTitleFontSize' => '18px',
                'postTitleTextColor' => '#ffffff',
                'postTitleFontSize' => '18px',
                'postBgColor' => '#004b4d',
            ],
            'accent-strip' => [
                'blockTitle' => 'Next Read',
                'blockTitleTextColor' => '#000000',
                'blockTitleFontSize' => '16px',
                'postTitleTextColor' => '#000000',
                'postTitleFontSize' => '16px',
                'postBgColor' => '#ffffff',
            ],
            'minimal-outline' => [
                'blockTitle' => 'New Post',
                'blockTitleTextColor' => '#1f2937',
                'blockTitleFontSize' => '17px',
                'postTitleTextColor' => '#0b6a78',
                'postTitleFontSize' => '16px',
                'postBgColor' => '#eaf8fb',
            ],
            'sleek-card' => [
                'blockTitle' => 'Quickly Read',
                'blockTitleTextColor' => '#173d4f',
                'blockTitleFontSize' => '18px',
                'postTitleTextColor' => '#000000',
                'postTitleFontSize' => '18px',
                'postBgColor' => '#ffffff',
            ],
            'compact' => [
                'blockTitle' => 'Best Read',
                'blockTitleTextColor' => '#4b5563',
                'blockTitleFontSize' => '18px',
                'postTitleTextColor' => '#ffffff',
                'postTitleFontSize' => '18px',
                'postBgColor' => '#0c3153',
            ],
        ];
    }
}

if (! function_exists('pocualrecb_sanitize_font_size')) {
    /**
     * Sanitizes font-size values for inline style usage.
     *
     * Accepts integer/decimal values with supported CSS units.
     * Unit-less numeric values are converted to pixel values.
     *
     * @param mixed  $pocualrecb_value         Raw value.
     * @param string $pocualrecb_fallback_size Fallback size if value is invalid.
     * @return string
     */
    function pocualrecb_sanitize_font_size($pocualrecb_value, $pocualrecb_fallback_size = '16px')
    {
        $pocualrecb_value = trim((string) $pocualrecb_value);

        if ('' === $pocualrecb_value) {
            return (string) $pocualrecb_fallback_size;
        }

        if (preg_match('/^\d+(?:\.\d+)?$/', $pocualrecb_value)) {
            return $pocualrecb_value . 'px';
        }

        if (preg_match('/^\d+(?:\.\d+)?(?:px|em|rem|%)$/', $pocualrecb_value)) {
            return $pocualrecb_value;
        }

        return (string) $pocualrecb_fallback_size;
    }
}

if (! function_exists('pocualrecb_get_style_field_defaults')) {
    /**
     * Returns default style values for a single template.
     *
     * @param string $pocualrecb_template Template slug.
     * @return array<string, string>
     */
    function pocualrecb_get_style_field_defaults($pocualrecb_template = 'default')
    {
        $pocualrecb_template = pocualrecb_sanitize_template($pocualrecb_template);
        $pocualrecb_template_defaults = pocualrecb_get_template_style_defaults();

        return $pocualrecb_template_defaults[ $pocualrecb_template ] ?? $pocualrecb_template_defaults['default'];
    }
}

if (! function_exists('pocualrecb_has_style_values')) {
    /**
     * Checks whether incoming style data has at least one provided field value.
     *
     * @param array<string, mixed> $pocualrecb_raw Incoming style values.
     * @return bool
     */
    function pocualrecb_has_style_values($pocualrecb_raw)
    {
        if (! is_array($pocualrecb_raw)) {
            return false;
        }

        $pocualrecb_style_fields = [
            'blockTitle',
            'blockTitleTextColor',
            'blockTitleFontSize',
            'postTitleTextColor',
            'postTitleFontSize',
            'postBgColor',
        ];

        foreach ($pocualrecb_style_fields as $pocualrecb_style_field) {
            if (! array_key_exists($pocualrecb_style_field, $pocualrecb_raw)) {
                continue;
            }

            $pocualrecb_value = sanitize_text_field((string) $pocualrecb_raw[ $pocualrecb_style_field ]);

            if ('' !== $pocualrecb_value) {
                return true;
            }
        }

        return false;
    }
}

if (! function_exists('pocualrecb_get_default_settings')) {
    /**
     * Returns plugin default settings.
     *
     * @return array<string, mixed>
     */
    function pocualrecb_get_default_settings()
    {
        $pocualrecb_template_styles = pocualrecb_get_template_style_defaults();
        $pocualrecb_style_defaults = pocualrecb_get_style_field_defaults('default');

        return array_merge(
            $pocualrecb_style_defaults,
            [
                'template' => 'default',
                'templateStyles' => $pocualrecb_template_styles,
            ]
        );
    }
}

if (! function_exists('pocualrecb_sanitize_style_settings')) {
    /**
     * Sanitizes block/post style settings.
     *
     * @param array<string, mixed> $pocualrecb_raw      Raw style values.
     * @param array<string, mixed> $pocualrecb_fallback Fallback style values.
     * @param string               $pocualrecb_template Template slug.
     * @return array<string, string>
     */
    function pocualrecb_sanitize_style_settings($pocualrecb_raw, $pocualrecb_fallback = [], $pocualrecb_template = 'default')
    {
        $pocualrecb_raw = is_array($pocualrecb_raw) ? $pocualrecb_raw : [];
        $pocualrecb_fallback = is_array($pocualrecb_fallback) ? $pocualrecb_fallback : [];
        $pocualrecb_style_defaults = pocualrecb_get_style_field_defaults($pocualrecb_template);

        $pocualrecb_resolve_color = static function ($pocualrecb_value, $pocualrecb_fallback_value, $pocualrecb_default_value) {
            $pocualrecb_sanitized = sanitize_hex_color((string) $pocualrecb_value);

            if (! empty($pocualrecb_sanitized)) {
                return $pocualrecb_sanitized;
            }

            $pocualrecb_sanitized_fallback = sanitize_hex_color((string) $pocualrecb_fallback_value);

            if (! empty($pocualrecb_sanitized_fallback)) {
                return $pocualrecb_sanitized_fallback;
            }

            return (string) $pocualrecb_default_value;
        };

        $pocualrecb_resolve_text = static function ($pocualrecb_value, $pocualrecb_fallback_value, $pocualrecb_default_value) {
            $pocualrecb_sanitized = sanitize_text_field((string) $pocualrecb_value);

            if ('' !== $pocualrecb_sanitized) {
                return $pocualrecb_sanitized;
            }

            $pocualrecb_sanitized_fallback = sanitize_text_field((string) $pocualrecb_fallback_value);

            if ('' !== $pocualrecb_sanitized_fallback) {
                return $pocualrecb_sanitized_fallback;
            }

            return (string) $pocualrecb_default_value;
        };

        $pocualrecb_resolve_font_size = static function ($pocualrecb_value, $pocualrecb_fallback_value, $pocualrecb_default_value) {
            $pocualrecb_sanitized = pocualrecb_sanitize_font_size($pocualrecb_value, '');

            if ('' !== $pocualrecb_sanitized) {
                return $pocualrecb_sanitized;
            }

            $pocualrecb_sanitized_fallback = pocualrecb_sanitize_font_size($pocualrecb_fallback_value, '');

            if ('' !== $pocualrecb_sanitized_fallback) {
                return $pocualrecb_sanitized_fallback;
            }

            return pocualrecb_sanitize_font_size($pocualrecb_default_value, '16px');
        };

        return [
            'blockTitle' => $pocualrecb_resolve_text(
                $pocualrecb_raw['blockTitle'] ?? '',
                $pocualrecb_fallback['blockTitle'] ?? '',
                $pocualrecb_style_defaults['blockTitle']
            ),
            'blockTitleTextColor' => $pocualrecb_resolve_color(
                $pocualrecb_raw['blockTitleTextColor'] ?? '',
                $pocualrecb_fallback['blockTitleTextColor'] ?? '',
                $pocualrecb_style_defaults['blockTitleTextColor']
            ),
            'blockTitleFontSize' => $pocualrecb_resolve_font_size(
                $pocualrecb_raw['blockTitleFontSize'] ?? '',
                $pocualrecb_fallback['blockTitleFontSize'] ?? '',
                $pocualrecb_style_defaults['blockTitleFontSize']
            ),
            'postTitleTextColor' => $pocualrecb_resolve_color(
                $pocualrecb_raw['postTitleTextColor'] ?? '',
                $pocualrecb_fallback['postTitleTextColor'] ?? '',
                $pocualrecb_style_defaults['postTitleTextColor']
            ),
            'postTitleFontSize' => $pocualrecb_resolve_font_size(
                $pocualrecb_raw['postTitleFontSize'] ?? '',
                $pocualrecb_fallback['postTitleFontSize'] ?? '',
                $pocualrecb_style_defaults['postTitleFontSize']
            ),
            'postBgColor' => $pocualrecb_resolve_color(
                $pocualrecb_raw['postBgColor'] ?? '',
                $pocualrecb_fallback['postBgColor'] ?? '',
                $pocualrecb_style_defaults['postBgColor']
            ),
        ];
    }
}

if (! function_exists('pocualrecb_get_sanitized_template_styles')) {
    /**
     * Sanitizes styles for all templates.
     *
     * @param array<string, mixed> $pocualrecb_template_styles Template style map.
     * @param array<string, mixed> $pocualrecb_legacy_styles   Fallback style values.
     * @param string               $pocualrecb_legacy_template Template used by legacy style fields.
     * @return array<string, array<string, string>>
     */
    function pocualrecb_get_sanitized_template_styles($pocualrecb_template_styles, $pocualrecb_legacy_styles = [], $pocualrecb_legacy_template = 'default')
    {
        $pocualrecb_template_styles = is_array($pocualrecb_template_styles) ? $pocualrecb_template_styles : [];
        $pocualrecb_legacy_template = pocualrecb_sanitize_template($pocualrecb_legacy_template);
        $pocualrecb_has_legacy_style_values = pocualrecb_has_style_values($pocualrecb_legacy_styles);
        $pocualrecb_legacy_styles = $pocualrecb_has_legacy_style_values
            ? pocualrecb_sanitize_style_settings($pocualrecb_legacy_styles, [], $pocualrecb_legacy_template)
            : [];
        $pocualrecb_sanitized_styles = [];

        foreach (array_keys(pocualrecb_get_template_choices()) as $pocualrecb_template_key) {
            $pocualrecb_template_raw = $pocualrecb_template_styles[ $pocualrecb_template_key ] ?? [];
            $pocualrecb_template_raw = is_array($pocualrecb_template_raw) ? $pocualrecb_template_raw : [];
            $pocualrecb_template_fallback = [];

            if (
                $pocualrecb_has_legacy_style_values &&
                $pocualrecb_template_key === $pocualrecb_legacy_template &&
                ! pocualrecb_has_style_values($pocualrecb_template_raw)
            ) {
                // Keep backward compatibility for users who saved old single-style settings.
                $pocualrecb_template_fallback = $pocualrecb_legacy_styles;
            }

            $pocualrecb_sanitized_styles[ $pocualrecb_template_key ] = pocualrecb_sanitize_style_settings(
                $pocualrecb_template_raw,
                $pocualrecb_template_fallback,
                $pocualrecb_template_key
            );
        }

        return $pocualrecb_sanitized_styles;
    }
}

if (! function_exists('pocualrecb_get_styles_for_template')) {
    /**
     * Returns sanitized styles for a single template.
     *
     * @param array<string, mixed> $pocualrecb_defaults Global defaults.
     * @param string               $pocualrecb_template Template slug.
     * @return array<string, string>
     */
    function pocualrecb_get_styles_for_template($pocualrecb_defaults, $pocualrecb_template)
    {
        $pocualrecb_defaults = is_array($pocualrecb_defaults) ? $pocualrecb_defaults : [];
        $pocualrecb_template = pocualrecb_sanitize_template($pocualrecb_template);
        $pocualrecb_fallback_styles = pocualrecb_sanitize_style_settings($pocualrecb_defaults, [], $pocualrecb_template);
        $pocualrecb_template_styles = $pocualrecb_defaults['templateStyles'] ?? [];
        $pocualrecb_template_styles = is_array($pocualrecb_template_styles) ? $pocualrecb_template_styles : [];
        $pocualrecb_template_style = $pocualrecb_template_styles[ $pocualrecb_template ] ?? [];
        $pocualrecb_template_style = is_array($pocualrecb_template_style) ? $pocualrecb_template_style : [];

        return pocualrecb_sanitize_style_settings($pocualrecb_template_style, $pocualrecb_fallback_styles, $pocualrecb_template);
    }
}

if (! function_exists('pocualrecb_get_global_defaults')) {
    /**
     * Returns merged global defaults with backward compatibility.
     *
     * @return array<string, mixed>
     */
    function pocualrecb_get_global_defaults()
    {
        $pocualrecb_plugin_defaults = pocualrecb_get_default_settings();
        $pocualrecb_saved = get_option('pocualrecb_defaults', []);
        $pocualrecb_saved = is_array($pocualrecb_saved) ? $pocualrecb_saved : [];
        $pocualrecb_template = pocualrecb_sanitize_template($pocualrecb_saved['template'] ?? $pocualrecb_plugin_defaults['template']);
        $pocualrecb_template_styles = pocualrecb_get_sanitized_template_styles(
            $pocualrecb_saved['templateStyles'] ?? [],
            $pocualrecb_saved,
            $pocualrecb_template
        );
        $pocualrecb_active_style = $pocualrecb_template_styles[ $pocualrecb_template ] ?? pocualrecb_get_style_field_defaults($pocualrecb_template);

        return array_merge(
            $pocualrecb_active_style,
            [
                'template' => $pocualrecb_template,
                'templateStyles' => $pocualrecb_template_styles,
            ]
        );
    }
}
