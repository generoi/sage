<?php

namespace App;

/**
 * Wrap oEmbeds in foundations responsive wrapper.
 */
add_filter('embed_oembed_html', function ($cache, $url, $attr, $post_id) {
  return '<div class="responsive-embed widescreen">' . $cache . '</div>';
}, 10, 4);

/**
 * Replace %category_name% in URLs. with the first category term.
 * @example
 * blog/%category%/my-post -> blog/action/my-post
 */
add_filter('post_type_link', function ($post_link, $post, $leavename, $sample) {
    if (preg_match('/%([^%]+)%/', $post_link, $matches)) {
        list($replace, $category) = $matches;
        $term = get_the_terms($post->ID, $category);
        if ($term && is_array($term)) {
            $slug = array_pop($term)->slug;
            $post_link = str_replace($replace, $slug, $post_link);
        }
    }
    return $post_link;
}, 10, 4);

/**
 * Theme assets in addition to sage/main.css and sage/main.js
 * @see src/setup.php
 */
add_action('wp_enqueue_scripts', function () {
    // wp_enqueue_style('font_css', '//fonts.googleapis.com/css?family=Open+Sans:400,600,700,800', false, null);

    // Scripts which are loaded synchronously
    wp_enqueue_script('theme/js/vendor', asset_path('scripts/vendor.js'), ['jquery'], false, true);
    wp_enqueue_script('theme/js/main', asset_path('scripts/main.js'), ['jquery', 'theme/js/vendor'], null, true);
}, 100);

add_action('wp_head', function () {
    // Load some styles asynchronously.
    Utils\print_async_stylesheet(asset_path('styles/icons.css'));
    // Use loadCSS as a fallback for asynchronously loading CSS in older browsers.
    // @see https://github.com/filamentgroup/loadCSS
    $loadcss_path = get_stylesheet_directory() . '/dist/scripts/loadcss.js';
    if (file_exists($loadcss_path)) {
        echo '<script>' . file_get_contents($loadcss_path) . '</script>';
    }
});

/**
 * Load scripts asynchronously
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
 * Dequeue assets.
 */
add_action('wp_print_scripts', function () {
     wp_dequeue_script('sage/main.js');

     // Fix debug-bar-js.dev.js referencing jQuery without depending on it.
     if (wp_script_is('debug-bar-js', 'enqueued')) {
        wp_dequeue_script('debug-bar-js');
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
        wp_enqueue_script('debug-bar-js', plugins_url("js/debug-bar-js$suffix.js", WP_PLUGIN_DIR . '/debug-bar'), ['jquery'], '20111216', true);
     }
}, 100);
add_action('wp_print_styles', function () {
     wp_dequeue_style('ext-widget-opts');
}, 100);

/**
 * Theme setup
 */
add_action('after_setup_theme', function () {
    /**
     * Use timber as a templating system.
     * @link https://github.com/generoi/wp-timber-extended
     */
    add_theme_support('timber-extended-templates', [
        // Use double dashes as the template variation separator.
        'bem_templates',
    ]);
    // If a post parent is password protected, so are it's children.
    add_theme_support('timber-extended-password-inheritance');
    // Add additional twig functions and filters.
    add_theme_support('timber-extended-twig-extensions', ['core', 'contrib', 'functional']);
    add_theme_support('timber-extended-timber-basics');

    /**
     * Register navigation menus in addition to `primary_navigation` defined by
     * sage.
     * @see src/setup.php
     */
    // register_nav_menus([
    //     'footer_navigation' => __('Footer Navigation', 'theme-admin')
    // ]);
}, 10);

/**
 * Remove Sage sidebars as we customize them.
 * @see src/setup.php
 */
remove_action('after_setup_theme', 'App\\widget_init');

/**
 * Register sidebars
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>'
    ];
    register_sidebar([
        'name'          => __('Primary', 'theme-admin'),
        'id'            => 'sidebar-primary'
    ] + $config);
    register_sidebar([
        'name'          => __('Footer', 'theme-admin'),
        'id'            => 'sidebar-footer'
    ] + $config);
    register_sidebar([
        'name'          => __('Below Content', 'theme-admin'),
        'id'            => 'sidebar-content-below'
    ] + $config);
});

/**
 * Load all post types on archive pages, not just posts.
 */
// add_filter('pre_get_posts', function ($query) {
//   if ((is_category() || is_tag() || is_tax()) && empty($query->query_vars['suppress_filters'])) {
//     $query->set('post_type', get_post_types());
//     return $query;
//   }
// });
