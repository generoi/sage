<?php

namespace App;

use GFAPI;
use GFExport;
use GFCommon;

/**
 * Create a demo Gravityform when the theme is activated.
 */
add_action('after_switch_theme', function () {
    if (!class_exists('GFAPI')) {
        return;
    }

    if (!empty(GFAPI::get_forms())) {
        return;
    }

    require_once GFCommon::get_base_path() . '/export.php';
    GFExport::import_file(get_stylesheet_directory() . '/resources/demo/gravityforms.json');
});

/**
 * Provide demo content when visiting /wp/wp-admin/post-new.php?gutenberg-demo
 */
add_filter('default_content', function ($content) {
    if (isset($_GET['gutenberg-demo'])) {
        $content = file_get_contents(get_stylesheet_directory() . '/resources/demo/gutenberg.html');
    }
    return $content;
}, 11);
