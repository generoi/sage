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
 * Customizer JS
 */
add_action('customize_preview_init', function () {
    wp_enqueue_script('sage/customizer.js', asset_path('scripts/customizer.js'), ['customize-preview'], null, true);
});

/**
 * Theme assets for the Admin Interface.
 */
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style('theme/css/admin', asset_path('styles/admin.css'));
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
 * Remove items from the admin bar.
 */
add_action('wp_before_admin_bar_render', function () {
    global $wp_admin_bar;
    // Yoast
    $wp_admin_bar->remove_menu('wpseo-menu');
});
