<?php

namespace App;

use function Roots\asset;

/**
 * Add a custom Block category for the theme
 */
add_filter('block_categories', function ($categories, $post) {
    $categories[] = [
        'slug' => 'sage',
        'title' => __('Theme blocks', 'sage'),
        'icon' => 'wordpress',
    ];
    return $categories;
}, 10, 2);

/**
 * Theme customizer
 */
add_action('customize_register', function (\WP_Customize_Manager $wp_customize) {
    // Add postMessage support
    $wp_customize->get_setting('blogname')->transport = 'postMessage';
    $wp_customize->selective_refresh->add_partial('blogname', [
        'selector' => '.brand',
        'render_callback' => function () {
            bloginfo('name');
        }
    ]);
});

/**
 * Limit available buttons in teenyMCE basic editor.
 * @see https://developer.wordpress.org/reference/classes/_wp_editors/editor_settings/
 */
add_filter('teeny_mce_buttons', function ($teeny_mce_buttons) {
    return ['bold', 'italic', 'undo', 'redo', 'wplink'];
});

/**
 * Add styling classes to TinyMCE.
 */
add_filter('tiny_mce_before_init', function ($settings) {
    $style_formats = [
        [
            'title' => 'Buttons',
            'items' => [
                ['title' => 'Buttons', 'selector' => 'a', 'classes' => 'button'],
            ],
        ],
    ];
    $settings['style_formats'] = json_encode($style_formats);
    $settings['style_formats_merge'] = true;
    return $settings;
});

/**
 * Modify buttons in TinyMCE's second row.
 */
add_filter('mce_buttons_2', function ($buttons) {
    // Unless TinyMCE Advanced is enabled, we need to specifically add the style button.
    array_splice($buttons, 1, 0, 'styleselect');
    $remove = [
        'forecolor', // text color
    ];
    return array_diff($buttons, $remove);
});

/**
 * Customizer JS
 */
add_action('customize_preview_init', function () {
    wp_enqueue_script('sage/customizer.js', asset('scripts/customizer.js'), ['customize-preview'], null, true);
});

/**
 * Theme assets for the Admin Interface.
 */
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('sage/admin.css', asset('styles/admin.css')->uri(), false, null);
    wp_enqueue_script('sage/admin', asset('scripts/admin.js')->uri(), ['jquery'], null);
    wp_add_inline_script('sage/admin', asset('scripts/manifest.js')->contents(), 'before');
});

/**
 * Block editor assets.
 */
add_action('enqueue_block_editor_assets', function () {
    wp_enqueue_style('sage/editor.css', asset('styles/editor.css')->uri(), ['wp-edit-blocks']);
    wp_enqueue_script('sage/vendor', asset('scripts/vendor.js')->uri(), []);
    wp_enqueue_script('sage/editor', asset('scripts/editor.js')->uri(), ['sage/vendor', 'wp-dom-ready', 'wp-edit-post', 'wp-blocks', 'wp-block-library', 'wp-i18n', 'wp-hooks', 'wp-components', 'wp-compose']);
    wp_add_inline_script('sage/vendor', asset('scripts/manifest.js')->contents(), 'before');
});
