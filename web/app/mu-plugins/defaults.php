<?php
/*
Plugin Name:  WP Defaults
Plugin URI:   https://genero.fi
Description:  Various defaults for WP
Version:      1.0.0
Author:       Genero
Author URI:   https://genero.fi/
License:      MIT License
*/

namespace Genero\Site;

use function \Sober\Intervention\intervention;

if (!is_blog_installed()) {
    return;
}

/**
 * Disable dashboard browse-happy requests / widget
 */
if (is_blog_admin() && !empty($_SERVER['HTTP_USER_AGENT'])) {
    add_filter('pre_site_transient_browser_' . md5($_SERVER['HTTP_USER_AGENT']), '__return_true');
}

/**
 * Disable dashboard php version widget (avoid unecessary SQL queries and HTTP requests)
 */
if (is_blog_admin()) {
    add_filter('pre_site_transient_php_check_' . md5(phpversion()), function ($default) {
        return ['is_acceptable' => true];
    });
}

/**
 * Prevent modification of .htaccess by WordPress.
 */
add_filter('flush_rewrite_rules_hard', '__return_false', 99, 1);

/**
 * Remove accents from media uploads
 */
add_filter('sanitize_file_name', 'remove_accents', 10, 1);

/**
 * Disable conversion of wysiwyg smilies codes to images
 */
add_filter('pre_option_use_smilies', '__return_zero', 10, 1);

/**
 * Disable XML-RPC
 */
add_filter('xmlrpc_enabled', '__return_false');

/**
 * Remove emojis and smilies hooks
 */
remove_action('init', 'smilies_init', 5);
remove_filter('the_content', 'convert_smilies', 20);
remove_filter('wp_mail', 'wp_staticize_emoji_for_email');

/**
 * @see https://github.com/Automattic/wp-super-cache/issues/397
 */
add_action('plugins_loaded', function () {
    remove_action('gc_cache', 'wpsc_timestamp_cache_update', 10, 2);
});

/**
 * Remove useless capital_P_dangit filter
 */
remove_filter('wp_title', 'capital_P_dangit', 11);
remove_filter('the_title', 'capital_P_dangit', 11);
remove_filter('the_content', 'capital_P_dangit', 11);
remove_filter('widget_text_content', 'capital_P_dangit', 11);
remove_filter('comment_text', 'capital_P_dangit', 31);

/**
 * Remove Yoast columns from Admin UI screen options by default.
 */
add_filter('default_hidden_columns', function () {
    $hidden[] = 'wpseo-score';
    $hidden[] = 'wpseo-score-readability';
    return $hidden;
}, 10, 2);

/**
 * Remove yoast from Admin bar.
 */
add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    $wp_admin_bar->remove_menu('wpseo-menu');
});

/**
 * Remove yoast from dashboard.
 */
add_action('wp_dashboard_setup', function () {
    remove_meta_box('wpseo-dashboard-overview', 'dashboard', 'normal');
});

/**
 * Remove nagging notices
 */
add_filter('pre_site_option_duplicate_post_show_notice', '__return_zero');
remove_action('admin_notices', 'woothemes_updater_notice');
remove_action('admin_notices', 'widgetopts_admin_notices');
add_action('admin_head', function () {
    if (!current_user_can('update_core')) {
        remove_action('admin_notices', 'update_nag', 3);
    }
});

/**
 * Disable SEO for post types without title and editor.
 */
add_filter('the_seo_framework_custom_post_type_support', function () {
    return ['title', 'editor'];
});

/**
 * Disable the SEO Bar columns on the post/page/taxonomy selection screens.
 */
add_filter('the_seo_framework_show_seo_column', '__return_false');

/**
 * Show Environment in Admin Bar color is backwards from a clients perspective
 */
add_action('wp_enqueue_scripts', __NAMESPACE__ . '\\shc_styling', 11);
add_action('admin_enqueue_scripts', __NAMESPACE__ . '\\shc_styling', 11);

function shc_styling()
{
    $styles = "
        #wpadminbar .ab-top-menu .shc-show-env.prod .ab-item,
        #wpadminbar .ab-top-menu .shc-show-env.prod:hover .ab-item {
            background-color: #46b450;
        }
        #wpadminbar .ab-top-menu .shc-show-env.dev .ab-item,
        #wpadminbar .ab-top-menu .shc-show-env.dev:hover .ab-item {
            background-color: #dc3232;
        }
    ";
    wp_add_inline_style('shc-show-env', $styles);
}

/*
 * @see https://github.com/soberwp/intervention/
 */
add_action('plugins_loaded', function () {
    if (!function_exists('\Sober\Intervention\intervention')) {
        return;
    }

    // @see https://github.com/soberwp/intervention/blob/master/.github/remove-help-tabs.md
    intervention('remove-help-tabs');

    // @see https://github.com/soberwp/intervention/blob/master/.github/remove-update-notices.md
    intervention('remove-update-notices', 'all-not-admin');

    // @see https://github.com/soberwp/intervention/blob/master/.github/remove-howdy.md
    intervention('remove-howdy');

    // @see https://github.com/soberwp/intervention/blob/master/.github/remove-emoji.md
    intervention('remove-emoji');

    // @see https://github.com/soberwp/intervention/blob/master/.github/remove-dashboard-items.md
    intervention('remove-dashboard-items', [
        'welcome',
        'notices',
        'recent-comments',
        'incoming-links',
        'plugins',
        'quick-draft',
        'drafts',
        'news',
    ], 'all');

    // @see https://github.com/soberwp/intervention/blob/master/.github/add-svg-support.md
    intervention('add-svg-support');

    // @see https://github.com/soberwp/intervention/blob/master/.github/update-pagination.md
    intervention('update-pagination', 50);

    // @see https://github.com/soberwp/intervention/blob/master/.github/update-label-footer.md
    intervention('update-label-footer', 'Created by Genero');
}, 99);