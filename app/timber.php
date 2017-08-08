<?php

/**
 * @file
 * Contains Timber and Twig configurations and theme specific twig
 * filters/functions.
 */

namespace App;

use Timber;
use TimberExtended;
use Twig_SimpleFilter;
use Genero\Sage\TwigExtensionLinkify;

/**
 * Define where to look for twig templates.
 */
add_action('after_setup_theme', function () {
    if (class_exists('Timber')) {
        Timber::$dirname = config('timber.dirname');
        Timber::$cache = config('timber.cache');
    }
});

/**
 * Site components injected into every timber context.
 */
add_filter('timber/context', function ($context) {
    $context['primary_menu'] = new TimberExtended\Menu('primary_navigation');
    $context['language_menu'] = new TimberExtended\LanguageMenu('language-menu');

    // Set the page title.
    $context['title'] = \App\title();

    // Add your sidebars.
    $context['sidebar__primary'] = Timber::get_widgets('sidebar-primary');
    $context['sidebar__footer'] = Timber::get_widgets('sidebar-footer');
    $context['sidebar__content_below'] = Timber::get_widgets('sidebar-content_below');

    // @todo inject somehow.
    if (is_tax() || is_tag() || is_category()) {
        $context['term'] = new Controller\Term();
    }

    if (function_exists('woocommerce_breadcrumb')) {
        $context['breadcrumb'] = Timber\Helper::ob_function('woocommerce_breadcrumb', [[
            'delimiter' => '',
            'wrap_before' => '<ul class="woocommerce-breadcrumb breadcrumbs">',
            'wrap_after' => '</ul>',
            'before' => '<li>',
            'after' => '</li>',
        ]]);
    }
    elseif (function_exists('yoast_breadcrumb')) {
        $context['breadcrumb'] = yoast_breadcrumb('', '', false);
    }

    // WooCommerce Menu Bar Cart integration.
    if (class_exists('WpMenuCart')) {
        $wp_menu_cart = new \WpMenuCart();
        $wp_menu_cart->load_classes();
        $context['wp_menu_cart'] = $wp_menu_cart->wpmenucart_menu_item();
    }

    return $context;
});

/**
 * Use the theme's TimberWidget class for Widgets.
 */
add_filter('timber_extended/class_name', function ($class_name, $types, $widget = null) {
    if (in_array('widget', $types)) {
        return 'Genero\\Sage\\TimberWidget';
    }
    return $class_name;
}, 10, 3);

/**
 * Use custom TimberPost subclasses.
 */
add_filter('Timber\PostClassMap', function ($post_class) {
    foreach (get_post_types(['_builtin' => false], 'objects') as $post_type) {
        $map[$post_type->name] = __NAMESPACE__ . '\\Controller\\Post';
    };
    $map['post'] = __NAMESPACE__ . '\\Controller\\Post';
    $map['page'] = __NAMESPACE__ . '\\Controller\\Post';
    $map['product'] = __NAMESPACE__ . '\\Controller\\ProductPost';
    return $map;
});

/**
 * Configure twig with functions and filters.
 */
add_filter('get_twig', function ($twig) {
    // macros/image.twig
    $twig->addGlobal('global_img_lazyload', true);
    $twig->addGlobal('global_img_crop', true);
    // layout/hero.twig
    $twig->addGlobal('global_video_poster', 'data:image/gif;base64,R0lGODlhAQABAIAAAP7//wAAACH5BAAAAAAALAAAAAABAAEAAAICRAEAOw==');
    $twig->addGlobal('global_slide_fallback', true);
    $twig->addGlobal('global_slide_defaults', ['slide_theme' => 'cover', 'slide_type' => 'image']);

    // Provide a `linkify` filter which transforms URL addresses to HTML links.
    // @example
    // {{ footnote|linkify }}
    $twig->addExtension(new TwigExtensionLinkify());

    // Use Finnish number format by default.
    // @example
    // {{ price|number_format }}
    $twig->getExtension('Twig_Extension_Core')->setNumberFormat(0, ',', ' ');

    // Get the asset path using Sage logic
    // @example
    // {{ asset_path('images/foo.svg') }}
    $twig->addFunction(new Timber\Twig_Function('asset_path', function ($filename) {
        return asset_path($filename);
    }));

    // Wrap the asset in a TimberImage object.
    // @example
    // {{ asset_image('images/foo.svg') }}
    $twig->addFunction(new Timber\Twig_Function('asset_image', function ($filename) {
        return new Timber\Image(asset_path($filename));
    }));

    // Format a phone number string.
    // @example
    // {{ post.phone|format_phone }}
    $twig->addFilter(new Twig_SimpleFilter('format_phone', function ($number) {
        return Utils\format_phone($number);
    }));

    /**
     * Strip everything except for numbers.
     * @example
     * <a href="tel:{{post.phone|tel}}">
     */
    $twig->addFilter(new Twig_SimpleFilter('tel', function ($number) {
        return preg_replace('/[^\+0-9]/', '', $number);
    }));

    /**
     * Return the value of a breakpoint in pixels without the unit.
     */
    $twig->addFunction(new Timber\Twig_Function('breakpoint', function ($breakpoint) {
        return Foundation\breakpoint($breakpoint);
    }));

    /**
     * Return the retina image URL as set by wp-retina-2x.
     */
    $twig->addFilter(new Twig_SimpleFilter('retina_url', function ($image) {
        if (function_exists('wr2x_get_retina_from_url')) {
            return wr2x_get_retina_from_url($image);
        }
        return false;
    }));

    $twig->addFunction(new Timber\Twig_Function('gform_field', function ($machine_name, $form_id) {
        $form = \GFFormsModel::get_form_meta($form_id);
        if (!isset($form['fields'])) {
            return 'form-not-found';
        }
        $field = wp_list_filter($form['fields'], ['machineName' => $machine_name]);
        if (empty($field)) {
            return 'field-not-found';
        }
        return reset($field);
    }));

    $twig->addFunction(new Timber\Twig_Function('gform', function ($form_id) {
        return \GFFormsModel::get_form_meta($form_id);
    }));

    /**
     * Return slick options based on how many slides to show at a time on
     * desktop.
     * @example
     * {% include 'parts/slideshow' with { items: posts, options: get_slick_options(3) } %}
     */
    $twig->addFunction(new Timber\Twig_Function('get_slick_options', function ($slides_to_show = 1, $extra_options = null) { // @codingStandardsIgnoreLine
        $tablet_slides = $mobile_slides = $desktop_slides = 1;

        if (is_array($slides_to_show)) {
            if (isset($slides_to_show[1])) {
                $tablet_slides = (int) $slides_to_show[1];
            }
            if (isset($slides_to_show[2])) {
                $mobile_slides = (int) $slides_to_show[2];
            }
            $desktop_slides = (int) $slides_to_show[0];
        } else {
            $desktop_slides = (int) $slides_to_show;
            if ($desktop_slides >= 3) {
                $tablet_slides = 2;
            }
        }

        // If more than two are shown at a time, set a responsive variation for
        // mobile devices.
        if ($desktop_slides > 1 || $mobile_slides > 1 || $tablet_slides > 1) {
            $options = [
                'slidesToShow' => $desktop_slides, // 1024+
                'slidesToScroll' => $desktop_slides,
                'responsive' => [
                    [
                        'breakpoint' => Foundation\breakpoint('large'), // 640 - 1023
                        'settings' => ['slidesToShow' => $tablet_slides, 'slidesToScroll' => $tablet_slides],
                    ],
                    [
                        'breakpoint' => Foundation\breakpoint('medium'), // 0 - 639
                        'settings' => ['slidesToShow' => $mobile_slides, 'slidesToScroll' => $mobile_slides],
                    ],
                ],
            ];
        } else {
            $options = [
                'slidesToShow' => (int) $desktop_slides,
            ];
        }
        if ($extra_options) {
            $options = array_merge($options, $extra_options);
        }
        return $options;
    }));

    return $twig;
});
