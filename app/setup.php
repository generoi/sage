<?php

namespace App;

use Roots\Sage\Container;
use Roots\Sage\Assets\JsonManifest;
use Roots\Sage\Config;

/**
 * Use Foundation XY-grid.
 */
add_filter('widget-options-extended/grid', function () {
    return 'xy-grid';
});

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
    // wp_enqueue_style('font_css', '//fonts.googleapis.com/css?family=Open+Sans:400,600,700,800', false, null);
    wp_enqueue_style('sage/main.css', asset_path('styles/main.css'), false, null);

    // Scripts which are loaded synchronously
    wp_enqueue_script('theme/js/vendor', asset_path('scripts/vendor.js'), ['jquery'], false, true);
    wp_enqueue_script('theme/js/main', asset_path('scripts/main.js'), ['jquery', 'theme/js/vendor'], null, true);
}, 100);

/**
 * Dequeue stylesheets.
 */
add_action('wp_print_styles', function () {
     wp_dequeue_style('ext-widget-opts');
}, 100);

/**
 * Asynchronously loaded CSS.
 */
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
 * Theme setup
 */
add_action('after_setup_theme', function () {
    /**
     * Enable features from Soil when plugin is activated
     * @link https://roots.io/plugins/soil/
     */
    add_theme_support('soil-clean-up');
    add_theme_support('soil-jquery-cdn');
    add_theme_support('soil-nav-walker');
    add_theme_support('soil-nice-search');
    // @todo causes issues with Timber.
    // add_theme_support('soil-relative-urls');

    /**
     * Enable plugins to manage the document title
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

    /**
     * Use timber as a templating system.
     * @link https://github.com/generoi/wp-timber-extended
     */
    add_theme_support('timber-extended-templates', [
        /** Use double dashes as the template variation separator. */
        'bem_templates',
    ]);
    /** If a post parent is password protected, so are it's children. */
    add_theme_support('timber-extended-password-inheritance');
    /** Add additional twig functions and filters. */
    add_theme_support('timber-extended-twig-extensions', ['core', 'contrib', 'functional']);
    add_theme_support('timber-extended-timber-basics');

    /**
     * Woocommerce support.
     */
    add_theme_support('woocommerce');
    // add_theme_support('wc-product-gallery-lightbox');

    /**
     * Register navigation menus
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', 'sage')
    ]);

    /**
     * Enable post thumbnails
     * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
     */
    add_theme_support('post-thumbnails');

    /**
     * Enable HTML5 markup support
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#html5
     */
    add_theme_support('html5', ['caption', 'comment-form', 'comment-list', 'gallery', 'search-form']);

    /**
     * Enable selective refresh for widgets in customizer
     * @link https://developer.wordpress.org/themes/advanced-topics/customizer-api/#theme-support-in-sidebars
     */
    add_theme_support('customize-selective-refresh-widgets');

    /**
     * Use main stylesheet for visual editor
     * @see resources/assets/styles/layouts/_tinymce.scss
     */
    add_editor_style(asset_path('styles/main.css'));
}, 20);

/**
 * Register sidebars
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '',
        'after_widget'  => '',
        'before_title'  => '<span class="is-hidden">',
        'after_title'   => '</span>'
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
        'id'            => 'sidebar-content_below'
    ] + $config);
});

/**
 * Setup Sage options
 */
add_action('after_setup_theme', function () {
    /**
     * Add JsonManifest to Sage container
     */
    sage()->singleton('sage.assets', function () {
        return new JsonManifest(config('assets.manifest'), config('assets.uri'));
    });
});
