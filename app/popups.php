<?php

namespace App;

/**
 * Customize the default values.
 */
add_filter('spu/metaboxes/default_options', function ($defaults) {
    return $defaults;
});

/**
 * Add custom trigger options.
 */
add_filter('popups-extended/trigger_options', function ($positions) {
    return $positions;
});

/**
 * Add custom positions..
 */
add_filter('popups-extended/positions', function ($positions) {
    return $positions;
});

/**
 * Add custom types.
 */
add_filter('popups-extended/types', function ($positions) {
    return $positions;
});

/**
 * Add custom themes.
 */
add_filter('popups-extended/themes', function ($positions) {
    $positions['primary'] = __('Primary', 'theme-admin');
    $positions['secondary'] = __('Secondary', 'theme-admin');
    $positions['white'] = __('Light', 'theme-admin');
    $positions['black'] = __('Dark', 'theme-admin');
    return $positions;
});
