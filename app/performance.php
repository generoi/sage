<?php

namespace App;

/**
 * Remove WPML assets.
 */
defined('ICL_DONT_LOAD_NAVIGATION_CSS') || define('ICL_DONT_LOAD_NAVIGATION_CSS', true);
defined('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS') || define('ICL_DONT_LOAD_LANGUAGE_SELECTOR_CSS', true);
defined('ICL_DONT_LOAD_LANGUAGES_JS') || define('ICL_DONT_LOAD_LANGUAGES_JS', true);

/**
 * Tell Autoptimizer not to concat inline CSS/JavaScript.
 */
add_filter('autoptimize_css_include_inline', '__return_false');
add_filter('autoptimize_js_include_inline', '__return_false');

/**
 * Disable Autoptimize for logged in users.
 */
add_filter('autoptimize_filter_noptimize', function () {
    return WP_CACHE ? is_user_logged_in() : true;
});

/**
 * Exclude certain JS files from Autoptimize.
 */
add_filter('autoptimize_filter_js_exclude', function ($exclude) {
    $scripts = [
        // Required by inline js wp.hooks.
        'facetwp/assets/js/src/event-manager.js',
    ];
    return $exclude . ', ' . implode(', ', $scripts);
});

/**
 * Remove Autoptimize from the admin toolbar.
 */
add_filter('autoptimize_filter_toolbar_show', '__return_false');

/**
 * Prefetch DNS for external resources.
 */
add_filter('wp_resource_hints', function ($hints, $relation_type) {
    switch ($relation_type) {
        case 'dns-prefetch':
            // $hints[] = 'https://use.typekit.net';

            // $upload_dir_domain = Utils\get_upload_dir_domain();
            // // If upload dir is on a separate domain, prefetch the dns.
            // if (strpos(site_url(), $upload_dir_domain) == false) {
            //     $hints[] = $upload_dir_domain;
            // }
            break;
    }
    return $hints;
}, 10, 2);

/**
 * Load scripts asynchronously.
 */
add_filter('script_loader_tag', function ($tag, $handle) {
    $async_handles = [
        'theme/js/main',
    ];
    if (in_array($handle, $async_handles)) {
        return str_replace(' src', ' async="async" src', $tag);
    }
    return $tag;
}, 10, 2);

/**
 * Disable emojicons.
 */
add_filter('emoji_svg_url', '__return_false');
add_action('init', function () {
    remove_action('admin_print_styles', 'print_emoji_styles');
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
    remove_filter('the_content_feed', 'wp_staticize_emoji');
    remove_filter('comment_text_rss', 'wp_staticize_emoji');
    add_filter('tiny_mce_plugins', function ($plugins) {
        if (is_array($plugins)) {
            return array_diff($plugins, array('wpemoji'));
        }
        return array();
    });
});
