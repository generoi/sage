<?php

namespace App\Foundation;

/**
 * Return a option list of the available palette colors the theme has.
 */
function palette($type = 'all')
{
    $palette = [
        'primary'   => __('Primary', 'theme-admin'),
        'secondary' => __('Secondary', 'theme-admin'),
        'white'     => __('White', 'theme-admin'),
        'black'     => __('Black', 'theme-admin'),
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
