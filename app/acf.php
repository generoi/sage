<?php

/**
 * @file
 * Contains Advanced Custom Fields additions and field defaults.
 */

namespace App;

use Genero\Component\ArchivePageComponent;
use Genero\Sage\OptionsPage;

/**
 * Associate pages with post types emulating archive pages.
 */
$archive_pages = new ArchivePageComponent();

/**
 * Activate ACF Options Page.
 */
$options = new OptionsPage();
add_action('after_switch_theme', function () use ($options) {
    $options->addAcfFieldgroup();
});

/**
 * Add foundation palette colors for hero overlays.
 */
add_filter('acf/load_field/name=slide_overlay', function ($field) {
    $field['choices'] = ['none' => __('None', '<example-project>')] + sage('foundation')->palette('overlay');
    return $field;
});

/**
 * Configure our Google Maps API key.
 */
add_filter('acf/settings/google_api_key', function ($value) {
    return '';
});
