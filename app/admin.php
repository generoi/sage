<?php

namespace App;

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
 * Fix gutenberg integration.
 */
add_filter('the_content', function ($content) {
    if (strpos($content, '<!-- wp:core') !== FALSE) {
        remove_filter('the_content', 'wpautop');
    }
    return $content;
}, 8);

/**
 * Customizer JS
 */
add_action('customize_preview_init', function () {
    wp_enqueue_script('sage/customizer.js', asset_path('scripts/customizer.js'), ['customize-preview'], null, true);
});

/**
 * Integrate with wp-customize-post.
 */
add_filter('customize_posts_partial_schema', function ($schema) {
    $schema['post_title']['selector'] = '.page__title';
    $schema['post_date']['selector'] = '.page__date';
    $schema['post_content']['selector'] = '.page__content';
    $schema['post_excerpt']['selector'] = '.teaser__excerpt';
    return $schema;
});

/**
 * Theme assets for the Admin Interface.
 */
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('theme/css/admin', asset_path('styles/admin.css'));
    wp_enqueue_script('theme/js/admin', asset_path('scripts/admin.js'), ['jquery'], null, true);
});

/**
 * Remove nagging notices.
 */
remove_action('admin_notices', 'woothemes_updater_notice');
remove_action('admin_notices', 'widgetopts_admin_notices');

/**
 * Fix debug-bar-js.dev.js referencing jQuery without depending on it.
 */
add_action('wp_print_scripts', function () {
     if (wp_script_is('debug-bar-js', 'enqueued')) {
        wp_dequeue_script('debug-bar-js');
        $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '.dev' : '';
        wp_enqueue_script('debug-bar-js', plugins_url("js/debug-bar-js$suffix.js", WP_PLUGIN_DIR . '/debug-bar'), ['jquery'], '20111216', true);
     }
}, 999);

/**
 * Hide some columns by default from the Admin UI screen options.
 */
add_filter('default_hidden_columns', function ($hidden, $screen) {
    if (!empty($screen->taxonomy)) {
        $hidden[] = 'description';
    }
    if (!empty($screen->post_type) && $screen->post_type == 'post') {
        $hidden[] = 'tags';
    }
    $hidden[] = 'wpseo-score';
    $hidden[] = 'wpseo-score-readability';
    return $hidden;
}, 10, 2);

/**
 * Add Foundation styling classes to TinyMCE.
 */
add_filter('tiny_mce_before_init', function ($settings) {
    $style_formats = [
        [
            'title' => 'Buttons',
            'items' => [
                ['title' => 'Buttons', 'selector' => 'a', 'classes' => 'button'],
                ['title' => 'Primary Color (Button)', 'selector' => 'a.button', 'classes' => 'primary'],
                ['title' => 'Secondary Color (Button)', 'selector' => 'a.button', 'classes' => 'secondary'],
                ['title' => 'Tiny (Button)', 'selector' => 'a.button', 'classes' => 'tiny'],
                ['title' => 'Small (Button)', 'selector' => 'a.button', 'classes' => 'small'],
                ['title' => 'Large (Button)', 'selector' => 'a.button', 'classes' => 'large'],
            ],
        ],
        [
            'title' => 'Callout',
            'items' => [
                ['title' => 'Callout box', 'block' => 'div', 'classes' => 'callout' ],
                ['title' => 'Primary (Callout)', 'selector' => 'div.callout', 'classes' => 'primary'],
                ['title' => 'Secondary (Callout)', 'selector' => 'div.callout', 'classes' => 'secondary'],
                ['title' => 'Success (Callout)', 'selector' => 'div.callout', 'classes' => 'success'],
                ['title' => 'Warning (Callout)', 'selector' => 'div.callout', 'classes' => 'warning'],
                ['title' => 'Alert (Callout)', 'selector' => 'div.callout', 'classes' => 'alert'],
            ],
        ],
    ];
    $settings['style_formats'] = json_encode($style_formats);
    $settings['style_formats_merge'] = true;

    // Fix constantly growing editor.
    if (!isset($settings['content_style'])) {
        $settings['content_style'] = '';
    }
    $settings['content_style'] = 'html { height: auto !important; min-height: initial !important; max-height: initial !important; }';

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
 * Remove items from the admin bar.
 */
add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    // Yoast
    $wp_admin_bar->remove_menu('wpseo-menu');
});
