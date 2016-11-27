<?php

namespace App;

/**
 * Add custom placeholders to yoast.
 *
 * - %%term_parent_title%%
 */
add_filter('wpseo_replacements', function ($replacements) {
    if (is_tax() || is_category() || is_tag()) {
        $term = $GLOBALS['wp_query']->get_queried_object();
        $taxonomy = $term->taxonomy;
        if ($term->parent != 0) {
            $parent = get_term($term->parent, $taxonomy);
            $replacements['%%term_parent_title%%'] = $parent->name;
        }
    }
    return $replacements;
});
