<?php

/**
 * @file
 * Contains main setup logic for the theme. Here assets, regions and
 * theme-support's are defined.
 */

namespace App;

use Roots\Sage\Container;
use Roots\Sage\Assets\JsonManifest;
use Roots\Sage\Config;
use Genero\Sage\Foundation;

/**
 * Use slide fallback for all single posts except for products.
 */
add_filter('wp-hero/fallback', function () {
    return is_single() && !is_singular('product');
});

/**
 * Use Foundation XY-grid.
 */
add_filter('widget-options-extended/grid', function () {
    return config('foundation.grid');
});
add_filter('tailor-foundation/grid', function () {
    return config('foundation.grid');
});

/**
 * Theme assets
 */
add_action('wp_enqueue_scripts', function () {
    // wp_enqueue_style('font/google', 'https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800', false, null);
    // wp_enqueue_style('font/typekit', 'https://use.typekit.net', false, null);
    wp_enqueue_style('sage/main.css', asset_path('styles/main.css'), false, null);

    // Scripts which are loaded synchronously
    wp_enqueue_script('sage/vendor.js', asset_path('scripts/vendor.js'), ['jquery'], false, true);
    wp_enqueue_script('sage/main.js', asset_path('scripts/main.js'), ['jquery', 'sage/vendor.js'], null, true);
    // We use wp-gravityforms-timber directly and not only through shortcodes.
    wp_enqueue_script('wp-gravityforms-timber/js');

    wp_localize_script('sage/main.js', 'Sage', [
        'language' => get_locale(),
        'l10n' => [
            'close' => __('Close', '<example-project>'),
            'loading' => __('Loading...', '<example-project>'),
            'previous' => __('Previous', '<example-project>'),
            'next' => __('Next', '<example-project>'),
            'counter' => __('%curr% of %total%', '<example-project>'),
            'image_not_loaded' => __('<a href="%url%">The image</a> could not be loaded', '<example-project>'),
            'content_not_loaded' => __('<a href="%url%">The content</a> could not be loaded', '<example-project>'),
        ],
    ]);
}, 100);

/**
 * Dequeue stylesheets.
 */
add_action('wp_print_styles', function () {
    wp_dequeue_style('ext-widget-opts'); // widget-options
    wp_dequeue_style('wp-blocks'); // gutenberg
    wp_dequeue_style('crp-style-rounded-thumbs'); // contextual related posts
    wp_dequeue_style('wp-smart-crop-renderer'); // wp-smartcrop
    wp_dequeue_script('jquery.wp-smartcrop'); // wp-smartcrop
    wp_dequeue_style('dashicons'); // wp core
}, 100);

/**
 * Asynchronously loaded CSS.
 */
add_action('wp_head', function () {
    // Load some styles asynchronously.
    Utils\print_async_stylesheet(asset_path('styles/icons.css'));
    // Use loadCSS as a fallback for asynchronously loading CSS in older browsers.
    // @see https://github.com/filamentgroup/loadCSS
    $loadcss_path = get_stylesheet_directory() . '/dist/scripts/icons.js';
    if (file_exists($loadcss_path)) {
        echo sprintf('<script>%s</script>', file_get_contents($loadcss_path));
    } else {
        echo sprintf('<script src="%s"></script>', asset_path('scripts/icons.js'));
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
        'widget',
        // 'tailor',
        // 'woocommerce',
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
     * Other plugins
     */
    add_theme_support('yoast-seo-breadcrumbs');

    /**
     * Register navigation menus
     * @link https://developer.wordpress.org/reference/functions/register_nav_menus/
     */
    register_nav_menus([
        'primary_navigation' => __('Primary Navigation', '<example-project>')
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
        'name'          => __('Primary', '<example-project>'),
        'id'            => 'sidebar-primary'
    ] + $config);
    register_sidebar([
        'name'          => __('Footer', '<example-project>'),
        'id'            => 'sidebar-footer'
    ] + $config);
    register_sidebar([
        'name'          => __('Below Content', '<example-project>'),
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

    /**
     * Add Foundation to Sage container
     */
    sage()->singleton('sage.foundation', function () {
        return new Foundation(config('foundation'));
    });
});
