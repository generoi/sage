<?php

/**
 * @file
 * Contains Advanced Custom Fields additions and field defaults.
 */

namespace App;

use Genero\Component\AcfFieldLoader;
use Genero\Component\HeroComponent;
use Genero\Component\ArchivePageComponent;
use Genero\Sage\OptionsPage;

/**
 * A banner field group available on posts and terms which can be used to
 * display slideshows in the header.
 */
$hero = new HeroComponent();
add_action('after_switch_theme', function () use ($hero) {
    $hero->addAcfFieldgroup();
});

/**
 * Associate pages with post types emulating archive pages.
 */
$archive_pages = new ArchivePageComponent();

/**
 * Activate ACF Option Page.
 */
$options = new OptionsPage();
add_action('after_switch_theme', function () use ($options) {
    $options->addAcfFieldgroup();
});

/**
 * Add foundation palette colors for hero overlays.
 */
add_filter('acf/load_field/name=slide_overlay', function ($field) {
    $field['choices'] = ['none' => __('None', '<example-project>')] + Foundation\palette('overlay');
    return $field;
});

/**
 * Configure our Google Maps API key.
 */
add_filter('acf/settings/google_api_key', function ($value) {
    return '';
});

/**
 * Collapse ACF fields by default.
 */
// add_action('acf/input/admin_footer', ['Genero\\Sage\\Acf', 'action_collapse_fields']);
