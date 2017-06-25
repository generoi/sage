<?php

namespace App;

/**
 * Remove slow function that repeatedly runs get_theme_mod().
 */
remove_action('tailor_control_args_colorpicker', 'tailor_modify_colorpicker');

/**
 * Remove duplicate or onsupported third party scripts.
 */
add_action('wp_print_scripts', function () {
    wp_dequeue_script('slick-slider');
    wp_dequeue_script('shuffle');
    wp_dequeue_script('magnific-popup');
    // wp_dequeue_script('google-maps-api');
    wp_enqueue_script('imagesloaded');
});

/**
 * Block the WP Editor when Tailor is active.
 */
add_action('admin_notices', function () {
    $screen = get_current_screen();
    if ($screen->base !== 'post') {
        return;
    }
    $post = get_post();
    if (get_post_meta($post->ID, '_tailor_layout', true) == false) {
        return;
    }
    echo "<style>
        .wp-editor-container:before,
        .mce-edit-area:before {
            content: '" . __('This page is managed by tailor and should not be edited directly. Click "Tailor this Page" in the toolbar.', 'theme-admin') . "';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2vw;
            white-space: normal;
            text-align: center;
            line-height: 1.5;
        }
    </style>";
});

/**
 * Integrate theme options with Tailor elements.
 */
add_action('tailor_element_register_controls', function ($element) {
    $setting = ['sanitize_callback' => 'tailor_sanitize_text'];

    // Add background and overlay color integration.
    switch ($element->tag) {
        case 'tailor_column':
        case 'tailor_row':
        case 'tailor_section':
        case 'tailor_content':
            $element->add_setting('background_theme', $setting);
            $element->add_setting('overlay_theme', $setting);

            $priority = 24;
            $element->add_control('background_theme', [
                'type' => 'select',
                'label' => __('Background', 'theme-admin'),
                'section' => 'attributes',
                'choices' => ['' => ''] + Foundation\palette('background'),
                'priority' => $priority++,
            ]);
            $element->add_control('overlay_theme', [
                'type' => 'select',
                'label' => __('Overlay', 'theme-admin'),
                'section' => 'attributes',
                'choices' => ['' => ''] + Foundation\palette('overlay'),
                'priority' => $priority++,
            ]);
            break;
        default:
            // Remove background image options from elements that shouldn't
            // use backgrounds.
            foreach (['background_image', 'background_size'] as $key) {
                $element->remove_control($key);
                $element->remove_setting($key);
            }
            break;
    }

    switch ($element->tag) {
        case 'tailor_section':
            $element->add_setting('container', $setting + ['default' => '1']);
            $element->add_control('container', [
                'label' => __('Contain content to predefined width'),
                'type' => 'switch',
                'choices' => ['1' => __('Contain content to grid')],
                'section' => 'general',
                'priority' => 25,
            ]);
    }

    switch ($element->tag) {
        // Integrate Foundation palette styles.
        case 'tailor_button':
            $style = $element->get_control('style');
            $style->choices = ['default'   => 'Default'] + Foundation\palette('button');
            break;
        // Integrate Foundation palette styles.
        case 'tailor_hero':
            $element->add_setting('style', $setting);
            $element->add_control('style', [
                'type' => 'select',
                'label' => __('Style', 'theme-admin'),
                'section' => 'general',
                'choices' => ['' => ''] + Foundation\palette(),
                'priority' => 25,
            ]);
            break;
        // Modify post listing layout options.
        case 'tailor_posts':
            $layouts = $element->get_control('layout');
            $layouts->choices = [
                'list'     => __('List', 'tailor'),
                'grid'     => __('Grid', 'tailor'),
                'carousel' => __('Carousel', 'tailor'),
            ];
            break;
    }
});

/**
 * Output our theme's CSS classes for styles.
 */
add_action('tailor_shortcode_html_attributes', function ($html_atts, $atts, $tag) {
    if (!empty($atts['background_theme'])) {
        $html_atts['class'][] = 'u-bg--' . $atts['background_theme'];
    }
    if (!empty($atts['style'])) {
        $html_atts['class'][] = $atts['style'];
    }
    if ($tag == 'tailor_section' && empty($atts['container'])) {
        $html_atts['class'][] = 'no-container';
    }
    if (!empty($atts['overlay_theme'])) {
        $html_atts['class'][] = 'overlay';
        $html_atts['class'][] = 'overlay--' . $atts['overlay_theme'];
    }
    return $html_atts;
}, 10, 3);

/**
 * Remove unsupported elements.
 */
add_action('tailor_register_elements', function ($elements) {
    foreach ([
        // 'tailor_carousel',
        // 'tailor_carousel_item',
        // 'tailor_grid',
        // 'tailor_grid_item',
        // 'tailor_list',
        // 'tailor_list_item',
        // 'tailor_map',
        // 'tailor_map_marker',
        // 'tailor_row',
        // 'tailor_column',
        'tailor_tabs',
        'tailor_tab',
        'tailor_toggles',
        // 'tailor_section',
        'tailor_card',
        // 'tailor_hero',
        'tailor_box',
        // 'tailor_posts',
        'tailor_gallery',
        // 'tailor_button',
        // 'tailor_content',
        'tailor_user',
        'tailor_widgets',
        'tailor_form_cf7',
        'tailor_jetpack_portfolio',
        'tailor_jetpack_testimonials',
    ] as $element) {
        $elements->remove_element($element);
    }
});
