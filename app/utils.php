<?php

namespace App\Utils;

/**
 * Format a phone number according to finnish system. Not perfect.
 */
function format_phone($number)
{
    // Remove all separators (except for parenthesis)
    $number = str_replace(['-', ' '], '', $number);
    // Format the ending numbers with spacing:
    // - 0407072916 -> 040 7072916
    // - 04007072916 -> 0400 7072916
    $number = preg_replace('|(\d{2,4})(\d{3})*(\d{4})$|', '$1 $2 $3', $number);
    // Format the spacing and the optional parenthesis:
    // +35840 -> +358 40
    // +358(0)40 -> +358 40
    $number = preg_replace('|^\+?358([\d])?(\(0\))?|', '+358 $1', $number);
    return $number;
}

/**
 * Print an asynchronously loaded stylesheet.
 * @see https://github.com/filamentgroup/loadCSS
 */
function print_async_stylesheet($path)
{
    echo '<link rel="preload" href="' . $path . '" as="style" onload="this.rel=\'stylesheet\'">';
    echo '<noscript><link rel="stylesheet" href="' . $path . '"></noscript>';
}

/**
 * Build a URL string based on the URL parts returned from `parse_url`.
 * @see https://stackoverflow.com/a/35207936/319855
 */
function build_url($parts)
{
    return (isset($parts['scheme']) ? "{$parts['scheme']}:" : '') .
        ((isset($parts['user']) || isset($parts['host'])) ? '//' : '') .
        (isset($parts['user']) ? "{$parts['user']}" : '') .
        (isset($parts['pass']) ? ":{$parts['pass']}" : '') .
        (isset($parts['user']) ? '@' : '') .
        (isset($parts['host']) ? "{$parts['host']}" : '') .
        (isset($parts['port']) ? ":{$parts['port']}" : '') .
        (isset($parts['path']) ? "{$parts['path']}" : '') .
        (isset($parts['query']) ? "?{$parts['query']}" : '') .
        (isset($parts['fragment']) ? "#{$parts['fragment']}" : '');
}

/**
 * Return the domain used for the upload directory. Useful if `upload_url_path`
 * is set to a subdomain.
 */
function get_upload_dir_domain() {
    $upload_dir = wp_upload_dir();
    $parts = parse_url($upload_dir['url']);
    $url = $parts['scheme'] . '://' . $parts['host'];
    return $url;
}

/**
 * Sort a list of terms hierarchicaly  Child terms will be placed under the
 * `children` property.
 * @see http://wordpress.stackexchange.com/a/99516
 */
function sort_terms_hierarchicaly(&$cats, &$into = null, $parent_id = 0)
{
    $has_target = isset($into);
    if (!$has_target) {
      $into = [];
    }
    foreach ($cats as $i => $cat) {
        if ($cat->parent == $parent_id) {
            $into[$cat->term_id] = $cat;
            unset($cats[$i]);
        }
    }
    foreach ($into as $top_cat) {
        $top_cat->children = array();
        sort_terms_hierarchicaly($cats, $top_cat->children, $top_cat->term_id);
    }
    if (!$has_target) {
        $cats = $into;
    }
}

/**
 * Return the first value found by looking hierarchicaly through an array or
 * object tree.
 *
 * @example
 * $terms = $this->get_terms('product_cat');
 * Utils\sort_terms_hierarchicaly($terms);
 * if ($terms) {
 *     return Utils\get_value_hierarchicaly('product_description', reset($terms));
 * }
 */
function get_value_hierarchicaly($field, $hierarchy, $child_property = 'children') {
    $found = null;
    if (is_array($hierarchy)) {
        if (!empty($hierarchy[$field])) {
            $found = $hierarchy[$field];
        }
        if (!empty($hierarchy[$child_property])) {
            foreach ($hierarchy[$child_property] as $child) {
                $child_value = get_value_hierarchicaly($field, $child);
                if (!empty($child_value)) {
                    $found = $child_value;
                }
            }
        }
    }
    elseif (is_object($hierarchy)) {
        if (!empty($hierarchy->$field)) {
            $found = $hierarchy->$field;
        }
        if (!empty($hierarchy->$child_property)) {
            foreach ($hierarchy->$child_property as $child) {
                $child_value = get_value_hierarchicaly($field, $child);
                if (!empty($child_value)) {
                    $found = $child_value;
                }
            }
        }
    }
    return $found;
}
