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
$requirements = [
    'Timber' => 'Timber',
    'TimberExtended' => 'Timber Extended',
];
foreach ($requirements as $class => $name) {
    if (!class_exists($class)) {
        $error = '<div class="error"><p>' . $name . ' not activated.</p></div>';
        if (!is_admin()) {
            echo $error;
        } else {
            add_action('admin_notices', function () use ($error) {
                echo $error;
            });
        }
    }
}

if (class_exists('Timber')) {
    Timber::$dirname = ['templates', 'templates/pages'];
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

    return $context;
});

/**
 * Preprocess meta data to be used in TimberPost objects.
 */
// add_filter('timber_post_get_meta_field', function ($value, $pid, $field_name, $post) {
//     switch ($field_name) {
//         case 'is_purchased':
//             $value = Genero\Sage\Woo\is_product_purchased_by_user($pid);
//             break;
//         case 'add_to_cart':
//             ob_start();
//             woocommerce_template_loop_add_to_cart(['class'=>'']);
//             $value = ob_get_clean();
//             break;
//
//     }
//     return $value;
// }, 12, 4);

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

    return $twig;
});
