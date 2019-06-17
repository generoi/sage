<?php

namespace App;

use function Roots\app;

/**
 * Get a list of all post types that the user might care about.
 */
function post_types(): array
{
    return collect(get_post_types(['_builtin' => false], 'objects'))
        ->pluck('label', 'name')
        ->except(['acf-field', 'acf-field-group', 'wp_stream_alerts', 'spucpt'])
        ->prepend(get_post_type_object('page')->labels->name, 'page')
        ->prepend(get_post_type_object('post')->labels->name, 'post')
        ->all();
}

/**
 * Build a URL string based on URL parts as returned by `parse_url()`
 * @see https://stackoverflow.com/a/35207936/319855
 */
function build_url(array $parts): string
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
 * Retrieve config values from tailwind configuration.
 *
 * @link https://github.com/approvedio/laravel-tailwind-config/blob/master/src/helpers.php
 */
function tailwind($key = null, $default = null)
{
    if (is_null($key)) {
        return app('tailwind');
    }

    return app('tailwind')->get($key, $default);
}
