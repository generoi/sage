<?php

namespace App;

use WPML_WordPress_Actions;
use Genero\Sage\Acf;

/**
 * Translation support:
 * 1. ACF Fields
 * 2. Widgets
 * 3. Slugs
 * 4. Custom hardcoded strings/links
 *
 * ---
 *
 * 1. ACF Fields
 *
 * Tell WPML to translate the Field Groups post type. Do not translate the
 * Widgets or Fields post types.
 *
 * Once done, create a duplicate translation of all field groups.
 *
 * ---
 *
 * 2. Widgets
 *
 * Use ACF field groups on ACF Widgets. See 1) ACF Fields.
 *
 * ---
 *
 * 3. Slugs
 * - Under Multilingual Content Setup in WPML Translation Management, check the
 * box for `Translate custom posts slugs`.
 * - In Pods or CPT, input the default language slug under `Custom Rewrite`.
 * - Also set `With Front` to false if you have a custom post slug.
 * - Under Custom Posts in WPML Translation Management, check the box for
 * `Use different slugs in different languages for X.`
 *
 * ---
 *
 * 4. Custom hardcoded strings/links
 *
 * Create new fields to the ACF Options page.
 *
 * @see wp-admin/admin.php?page=wpml-translation-management%2Fmenu%2Fmain.php&sm=mcsetup
 * @see https://www.advancedcustomfields.com/resources/multilingual-custom-fields/
 * @see https://wpml.org/documentation/getting-started-guide/translating-page-slugs/
 */

/**
 * Remove multilingual text functionality from wpml-string-translation if we're
 * using ACF Widgets.
 */
add_filter('acfw_include_widgets', function ($widgets) {
    remove_action('in_widget_form', 'icl_widget_text_in_widget_form_hook');
    return $widgets;
});

/**
 * Save widget ACF data by language. This filter is called just before ACF's.
 */
add_filter('widget_update_callback', function ($instance, $new_instance, $old_instance, $widget) {
    if (!function_exists('acf_get_setting')) {
        return $instance;
    }
    if (!Acf::is_default_language()) {
        $widget->id .= '_' . $current_language;
    }
    return $instance;
}, 9, 4);

/**
 * Load Widget field values by language.
 */
add_filter('acf/load_value', __NAMESPACE__ . '\\acf_load_widget_translated_value', 10, 3);
function acf_load_widget_translated_value($value, $post_id, $field) {
    if (Acf::is_default_language()) {
        return $value;
    }

    if (strpos($post_id, 'widget_') !== 0) {
        return $value;
    }

    // @see widget_update_callback filter in this file.
    $translated_id = $post_id . '_' . $current_language;
    $value = Acf::load_value_without_filters($translated_id, $field);

    return $value;
}

/**
 * Allow language fallback value for certain field types.
 */
add_filter('acf/load_value', function ($value, $post_id, $field) {
    // Ensure this value is empty.
    if (!Acf::is_value_empty($value)) {
        return $value;
    }
    // Ensure this is a translated version.
    if (!acf_get_setting('current_language') || Acf::is_default_language()) {
        return $value;
    }

    // Fallback on options which query options_LANGCODE.
    $force_fallback = false;
    if (strpos($post_id, 'options') === 0) {
        $post_id = 'options';
        $force_fallback = true;
    }

    $fallback_value = Acf::load_value_in_language($post_id, $field, acf_get_setting('default_language'));

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
        // Questionable. @todo test
        case 'post_object':
        case 'page_link':
        case 'relationship':
        case 'taxonomy':
        case 'user':
        case 'tab':
        case 'repeater':
        case 'flexible_content':
        case 'clone':
            return $fallback_value;
        // Always use the original value.
        case 'text':
        case 'textarea':
        case 'smart_button':
        case 'wysiwyg':
        case 'message':
            return $value;
    }

    return $value;
}, 11, 3);
