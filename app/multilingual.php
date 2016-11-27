<?php

namespace App;

use Genero\Sage\Acf;

/**
 * Integrate Polylang with ACF.
 */
// add_filter('acf/settings/default_language', function ($language) {
//     return pll_default_language();
// });
// add_filter('acf/settings/current_language', function ($language) {
//     return pll_current_language();
// });

/**
 * Remove multilingual text functionality from wpml-string-translation if we're
 * using ACF Widgets.
 */
add_filter('acfw_include_widgets', function ($widgets) {
    remove_action('in_widget_form', 'icl_widget_text_in_widget_form_hook');
    return $widgets;
});

/**
 * Allow language fallback value for certain field types.
 */
add_filter('acf/load_value', function ($value, $post_id, $field) {
    // Ensure this value is empty.
    if (!Acf::is_value_empty($value)) {
        return $value;
    }
    // Ensure this is a translated version.
    if (!Acf::get_language() || Acf::is_default_language()) {
        return $value;
    }
    // Fallback on options which query options_LANGCODE.
    $force_fallback = false;
    if (strpos($post_id, 'options') === 0) {
        $post_id = 'options';
        $force_fallback = true;
    }
    $fallback_value = Acf::load_value_in_language($post_id, $field, Acf::get_default_language());
    // Allow for certain fields to always fallback, eg options page.
    if ($force_fallback) {
        return $fallback_value;
    }
    switch ($field['type']) {
        // Always fallback on these.
        case 'number':
        case 'email':
        case 'url':
        case 'password':
        case 'oembed':
        case 'image':
        case 'file':
        case 'gallery':
        case 'image_crop':
        case 'select':
        case 'checkbox':
        case 'radio':
        case 'true_false':
        case 'post_type_chooser':
        case 'google_map':
        case 'date_picker':
        case 'date_time_picker':
        case 'time_picker':
        case 'color_picker':
        case 'number_slider':
        case 'foundation_column':
        case 'foundation_row':
            return $fallback_value;
        // Always use the original value.
        case 'post_object':
        case 'taxonomy':
        case 'user':
        case 'tab':
        case 'repeater':
        case 'flexible_content':
        case 'clone':
        case 'page_link':
        case 'relationship':
        case 'text':
        case 'textarea':
        case 'smart_button':
        case 'wysiwyg':
        case 'message':
            return $value;
    }
    return $value;
}, 11, 3);
