<?php

namespace App;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && !is_front_page()) {
        if (!in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Add class if sidebar is active */
    if (display_sidebar()) {
        $classes[] = 'sidebar-primary';
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-blade(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    /** Rename archive body class as it's the same as the archive content template. */
    if (($key = array_search('archive', $classes)) !== FALSE) {
        $classes[$key] = 'archive-page';
    }

    return array_filter($classes);
});

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
});

/**
 * Wrap oEmbeds in foundations responsive wrapper.
 */
add_filter('embed_oembed_html', function ($cache, $url, $attr, $post_id) {
    return '<div class="responsive-embed widescreen">' . $cache . '</div>';
}, 10, 4);
