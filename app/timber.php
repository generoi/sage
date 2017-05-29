<?php

namespace App;

use Timber;
use TimberExtended;
use Twig_SimpleFunction;
use Twig_SimpleFilter;
use Genero\Sage\TwigExtensionLinkify;
use Genero\Sage\PostTypeConnection;

/**
 * Define where to look for twig templates.
 *
 * Rather than adding a multitude of directories, consider prefixing the
 * included templates with the directory name: `parts/hero.twig`
 */
if (class_exists('Timber')) {
    Timber::$dirname = ['views', 'views/pages'];
    Timber::$cache = defined('WP_CACHE') ? WP_CACHE : false;
}

/**
 * Site components injected into every timber context.
 */
add_filter('timber/context', function ($context) {
    // Add your menus.
    if (function_exists('get_field')) {
        $context['primary_menu'] = new PostTypeConnection\Menu('primary_navigation');
    } else {
        $context['primary_menu'] = new TimberExtended\Menu('primary_navigation');
    }
    $context['language_menu'] = new TimberExtended\LanguageMenu('language-menu');

    // Set the page title.
    $context['title'] = \App\title();

    // Add your sidebars.
    $context['sidebar_primary'] = Timber::get_widgets('sidebar-primary');
    $context['sidebar_footer'] = Timber::get_widgets('sidebar-footer');
    $context['sidebar_content_below'] = Timber::get_widgets('sidebar-content-below');

    // @todo inject somehow.
    if (is_tax() || is_tag() || is_category()) {
        $context['term'] = new Term();
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
    // return [
    //     'post' => __NAMESPACE__ . '\\Post',
    //     'page' => '\\Timber\\Post',
    //     'product' => __NAMESPACE__ . '\\ProductPost',
    // ];
    return __NAMESPACE__ . '\\Post';
});

/**
 * Configure twig with functions and filters.
 */
add_filter('get_twig', function ($twig) {
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
    $twig->addFunction('asset_path', new Twig_SimpleFunction('asset_path', function ($filename) {
        return asset_path($filename);
    }));

    // Wrap the asset in a TimberImage object.
    // @example
    // {{ asset_image('images/foo.svg') }}
    $twig->addFunction('asset_image', new Twig_SimpleFunction('asset_image', function ($filename) {
        return new Timber\Image(asset_path($filename));
    }));

    // Format a phone number string.
    // @example
    // {{ post.phone|format_number }}
    $twig->addFilter('format_phone', new Twig_SimpleFilter('format_phone', function ($number) {
        return Utils\format_phone($number);
    }));

    /**
     * Strip everything except for numbers.
     * @example
     * <a href="tel:{{post.phone|tel}}">
     */
    $twig->addFilter('tel', new Twig_SimpleFilter('tel', function ($number) {
        return preg_replace('/[^0-9]/', '', $number);
    }));

    /**
     * Return slick options based on how many slides to show at a time on
     * desktop.
     * @example
     * {% include 'parts/slideshow' with { items: posts, options: get_slick_options(3) } %}
     */
    $twig->addFunction('get_slick_options', new Twig_SimpleFunction('get_slick_options', function ($slides_to_show = 1, $extra_options = null) {
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
        }
        else {
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
