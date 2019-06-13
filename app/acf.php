<?php

namespace App;

/**
 * Prepopulate available post types to acf fields.
 */
add_filter('acf/load_field/name=post_type', function ($field) {
    $field['choices'] = post_types();
    return $field;
});
