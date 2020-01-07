<?php

/**
 * Theme filters.
 *
 * @copyright https://roots.io/ Roots
 * @license   https://opensource.org/licenses/MIT MIT
 */

namespace App;

/**
 * Add "… Continued" to the excerpt.
 *
 * @return string
 */
add_filter('excerpt_more', function () {
    return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
});

/**
 * Remove wpautop filter if there are blocks found.
 */
foreach (['the_content', 'spu/popup/content'] as $filter) {
    add_filter($filter, function ($content) use ($filter) {
        if (has_blocks($content)) {
            remove_filter($filter, 'wpautop');
        }
        return $content;
    }, 8);
}

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

/**
 * Remove Sage's views directory structure from the path that Gutenberg's Block
 * template feature uses.
 */
add_filter('template_include', function ($template_file) {
    global $_wp_current_template_hierarchy;
    if (is_array($_wp_current_template_hierarchy)) {
        $_wp_current_template_hierarchy = array_map('basename', $_wp_current_template_hierarchy);
    }
    return $template_file;
}, 19);
