<?php

namespace App;

use function Roots\view;

/**
 * Add <body> classes
 */
add_filter('body_class', function (array $classes) {
    /** Add page slug if it doesn't exist */
    if (is_single() || is_page() && ! is_front_page()) {
        if (! in_array(basename(get_permalink()), $classes)) {
            $classes[] = basename(get_permalink());
        }
    }

    /** Clean up class names for custom templates */
    $classes = array_map(function ($class) {
        return preg_replace(['/-blade(-php)?$/', '/^page-template-views/'], '', $class);
    }, $classes);

    return array_filter($classes);
});

/**
 * Add "… Continued" to the excerpt
 */
add_filter('excerpt_more', function () {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
});

/**
 * Render WordPress searchform using Blade
 */
add_filter('get_search_form', function () {
    return view('forms.search');
});

/**
 * Modify oEmbed URL parameters.
 */
add_filter('embed_oembed_html', function ($cache, $url, $attr, $post_id) {
    preg_match('/src="([^"]*)"/i', $cache, $sources);
    if (!empty($sources)) {
        $src = $sources[1];
    }
    if (!empty($src) && !empty($url)) {
        $is_youtube = strpos($src, 'youtube') !== false;
        $is_vimeo = strpos($src, 'vimeo') !== false;
        if ($is_youtube) {
            // @see https://developers.google.com/youtube/player_parameters#Parameters
            $args = [
                'rel' => 0,
                'modestbranding' => 1,
            ];
        } elseif ($is_vimeo) {
            $args = [
                'title' => 0,
                'byline' => 0,
                'portrait' => 0,
                'controls' => 0,
                'iv_load_policy' => 3,
            ];
        }
        if (!empty($args) && ($parts = parse_url($src))) {
            $query = !empty($parts['query']) ? wp_parse_args($parts['query']) : [];
            // Override URL attributes with shortcode ones.
            $query = array_merge($query, $attr);
            // Add in defaults unless they are already defined.
            $query = array_merge($args, $query);
            // Force /embed endpoint for youtube.
            if ($is_youtube && $parts['path'] == '/watch') {
                $parts['path'] = '/embed/' . $query['v'];
                unset($query['v']);
            }
            if ($is_vimeo && is_numeric(substr($parts['path'], 1))) {
                $parts['host'] = 'player.vimeo.com';
                $parts['path'] = "/video{$parts['path']}";
            }
            // Use schemeless URL and re-build the query.
            $parts['scheme'] = null;
            $parts['query'] = build_query($query);
            // Rebuild the URL
            $url = build_url($parts);
            $cache = str_replace($src, $url, $cache);
        }
    }
    return $cache;
}, 10, 4);
