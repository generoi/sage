<?php

namespace App;

use function Roots\asset;
use function Roots\config;
use function Roots\view;

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
    wp_enqueue_script('sage/vendor', asset('scripts/vendor.js')->uri(), [], null);
    wp_enqueue_script('sage/app', asset('scripts/app.js')->uri(), ['sage/vendor'], null);

    wp_add_inline_script('sage/vendor', asset('scripts/manifest.js')->contents(), 'before');

    if (is_single() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }

    wp_enqueue_style('sage/reset.css', asset('styles/reset.css')->uri(), false, null);
    wp_enqueue_style('sage/app.css', asset('styles/app.css')->uri(), ['sage/reset.css'], null);
}, 100);

add_action('enqueue_block_assets', function () {
    wp_enqueue_script('sage/blocks', asset('scripts/blocks.js')->uri(), ['sage/vendor']);
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
    add_theme_support('soil-relative-urls');

    /**
     * Enable plugins to manage the document title
     * @link https://developer.wordpress.org/reference/functions/add_theme_support/#title-tag
     */
    add_theme_support('title-tag');

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
     * Gutenberg support
     * @link https://wordpress.org/gutenberg/handbook/extensibility/theme-support/
     */
    add_theme_support('editor-styles');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('disable-custom-colors');
    add_theme_support('disable-custom-font-sizes');
    add_theme_support('responsive-embeds');

    add_theme_support('editor-color-palette', [
        [
            'name' => __('Primary', 'sage'),
            'slug' => 'primary',
            'color' => tailwind('theme.colors.primary'),
        ],
        [
            'name' => __('Secondary', 'sage'),
            'slug' => 'secondary',
            'color' => tailwind('theme.colors.secondary'),
        ],
        [
            'name' => __('Black', 'sage'),
            'slug' => 'black',
            'color' => tailwind('theme.colors.black'),
        ],
        [
            'name' => __('White', 'sage'),
            'slug' => 'white',
            'color' => tailwind('theme.colors.white'),
        ],
    ]);

    add_theme_support('editor-font-sizes', [
        [
            'name' => __('xsmall', 'sage'),
            'shortName' => __('XS', 'sage'),
            'slug' => 'xs',
            'size' => tailwind('theme.fontSize.xs'),
        ],
        [
            'name' => __('small', 'sage'),
            'shortName' => __('S', 'sage'),
            'slug' => 'sm',
            'size' => tailwind('theme.fontSize.sm'),
        ],
        [
            'name' => __('regular', 'sage'),
            'shortName' => __('R', 'sage'),
            'slug' => 'base',
            'size' => tailwind('theme.fontSize.base'),
        ],
        [
            'name' => __('large', 'sage'),
            'shortName' => __('L', 'sage'),
            'slug' => 'xl',
            'size' => tailwind('theme.fontSize.xl'),
        ],
        [
            'name' => __('xlarge', 'sage'),
            'shortName' => __('XL', 'sage'),
            'slug' => 'xxl',
            'size' => tailwind('theme.fontSize.2xl'),
        ],
    ]);

    /**
     * Use main stylesheet for visual editor
     * @see resources/assets/styles/layouts/tinymce.scss
     */
    add_editor_style(asset('styles/app.css')->uri());
}, 20);

/**
 * Register sidebars
 */
add_action('widgets_init', function () {
    $config = [
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ];

    register_sidebar([
        'name' => __('Primary', 'sage'),
        'id' => 'sidebar-primary'
    ] + $config);

    register_sidebar([
        'name' => __('Footer', 'sage'),
        'id' => 'sidebar-footer'
    ] + $config);
});
