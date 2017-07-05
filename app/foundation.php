<?php

namespace App\Foundation;

/**
 * Return a option list of the available palette colors the theme has.
 */
function palette($type = 'all')
{
    $palette = [
        'primary'   => __('Primary', '<example-project>'),
        'secondary' => __('Secondary', '<example-project>'),
        'white'     => __('White', '<example-project>'),
        'black'     => __('Black', '<example-project>'),
    ];

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
    $breakpoints = [
        'small'   => 0,
        'medium'  => 640,
        'large'   => 1024,
        'xlarge'  => 1200,
        'xxlarge' => 1440,
    ];
    return isset($type) ? $breakpoints[$type] : $breakpoints;
}

/**
 * Return the font size in pixels.
 */
function fontsize($breakpoint = 'small')
{
    if (breakpoint($breakpoint) >= breakpoint('large')) {
        return 18;
    }
    return 16;
}

/**
 * Return the length of a paragraph.
 */
function paragraph_width($breakpoint = 'small')
{
    $max_width = (45 * fontsize($breakpoint));
    $breakpoints = breakpoint();
    if (!isset($breakpoints[$breakpoint])) {
        return null;
    }
    // Advance until the requested breakpoint
    while (key($breakpoints) !== $breakpoint) next($breakpoints);

    // Check if this breakpoint spans beyond the max width.
    if (next($breakpoints) > $max_width) {
        return $max_width . 'px';
    }

    // Return approximate viewport based width.
    return '95vw';
}
