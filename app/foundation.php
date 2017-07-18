<?php

namespace App\Foundation;

use App;

/**
 * Return a option list of the available palette colors the theme has.
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
 * Return a Foundation media breakpoint.
 */
function breakpoint($type = null)
{
    $breakpoints = App\config('foundation.breakpoint');
    return isset($type) ? $breakpoints[$type] : $breakpoints;
}

/**
 * Return the font size in pixels.
 */
function fontsize($breakpoint = 'small')
{
    $fontsizes = App\config('foundation.fontsize');

    foreach ($fontsizes as $_breakpoint => $fontsize) {
        if ($breakpoint == $_breakpoint) {
            return $fontsize;
        }
        if (breakpoint($breakpoint) > breakpoint($_breakpoint)) {
            return $fontsize;
        }
    }
}

/**
 * Return the length of a paragraph.
 */
function paragraph_width($breakpoint = 'small')
{
    $max_width = (App\config('foundation.paragraph_width') * fontsize($breakpoint));
    $breakpoints = breakpoint();
    // Advance until the requested breakpoint
    while (key($breakpoints) !== $breakpoint) next($breakpoints);

    // Check if this breakpoint spans beyond the max width.
    if (next($breakpoints) > $max_width) {
        return $max_width . 'px';
    }

    // Return approximate viewport based width.
    return '95vw';
}
