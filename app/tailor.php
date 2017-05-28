<?php

namespace App;

/**
 * Return a option list of the available palette colors the theme uses.
 */
function foundation_palette($type = 'all')
{
    $palette = [
        'primary'   => __('Primary', 'theme-admin'),
        'secondary' => __('Secondary', 'theme-admin'),
        'white'     => __('White', 'theme-admin'),
        'black'     => __('Black', 'theme-admin'),
    ];

    switch ($type) {
        case 'button':
            $colors = ['primary', 'secondary'];
            return array_intersect_key($palette, array_combine($colors, $colors));
        default:
            return $palette;
    }

}

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
            $element->add_control('background_theme', [
                'type' => 'select',
                'label' => __('Background', 'theme-admin'),
                'section' => 'attributes',
                'choices' => ['' => ''] + foundation_palette('background'),
                'priority' => 24,
            ]);
            $element->add_control('overlay_theme', [
                'type' => 'select',
                'label' => __('Overlay', 'theme-admin'),
                'section' => 'attributes',
                'choices' => ['' => ''] + foundation_palette('overlay'),
                'priority' => 25,
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
        // Integrate Foundation palette styles.
        case 'tailor_button':
            $style = $element->get_control('style');
            $style->choices = ['default'   => 'Default'] + foundation_palette('button');
            break;
        // Integrate Foundation palette styles.
        case 'tailor_hero':
            $element->add_setting('style', $setting);
            $element->add_control('style', [
                'type' => 'select',
                'label' => __('Style', 'tailor-foundation'),
                'section' => 'general',
                'choices' => ['' => ''] + foundation_palette(),
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
    if (!empty($atts['spacing'])) {
        $html_atts['class'][] = 'u-spacing--' . $atts['background_theme'];
    }
    if (!empty($atts['background_theme'])) {
        $html_atts['class'][] = 'u-bg--' . $atts['background_theme'];
    }
    if (!empty($atts['style'])) {
        $html_atts['class'][] = $atts['style'];
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
