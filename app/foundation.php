<?php

/**
 * @file
 * Contains utility functions for Foundation in PHP. Values are configured in
 * config/foundation.php
 */

namespace App\Foundation;

use App;

/**
 * Get a option list of the available palette colors the theme has.
 * @param string $type
 * @return array
 */
function palette($type = 'all')
{
    $palette = App\config('foundation.palette');

    switch ($type) {
        case 'button':
            $colors = ['primary', 'secondary'];
            return array_intersect_key($palette, array_combine($colors, $colors));
        default:
            return $palette;
    }
}

/**
 * Get a Foundation media breakpoint in pixels.
 * @param string $name
 * @return int|array
 */
function breakpoint($name = null)
{
    $breakpoints = App\config('foundation.breakpoint');
    return isset($name) ? $breakpoints[$name] : $breakpoints;
}

/**
 * Get Return the font size for the specified breakpoint in pixels.
 * @param string $breakpoint
 * @return int|array
 */
function fontsize($breakpoint = 'small')
{
    $fontsizes = App\config('foundation.fontsize');

    foreach ($fontsizes as $_breakpoint => $fontsize) {
        if ($breakpoint == $_breakpoint) {
            return $fontsize;
        }
        // If `small` and `large` are configured, `medium` should return `large`
        if (breakpoint($_breakpoint) > breakpoint($breakpoint)) {
            return $fontsize;
        }
    }
}

/**
 * Get the length of a paragraph as a CSS value including it's unit.
 * @param string $breakpoint
 * @return string
 */
function paragraph_width($breakpoint = 'small')
{
    $max_width = (App\config('foundation.paragraph_width') * fontsize($breakpoint));
    $breakpoints = breakpoint();
    // Advance until the requested breakpoint
    while (key($breakpoints) !== $breakpoint) {
        next($breakpoints);
    }

    // Check if this breakpoint spans beyond the max width.
    if (next($breakpoints) > $max_width) {
        return $max_width . 'px';
    }

    // Return approximate viewport based width.
    return '95vw';
}
