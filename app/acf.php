<?php

namespace App;

use Genero\Component\AcfFieldLoader;
use Genero\Component\HeroComponent;

use Genero\Sage\PostTypeConnection;
use Genero\Sage\OptionsPage;

/**
 * Use PostTypeConnection which adds a repeater field to the ACF Options page,
 * that connects post types to pages. This can be used to detect parent menu
 * items using the PostTypeConnection\Menu class.
 * @note https://github.com/generoi/acf-post-type-chooser is a dependency.
 */
AcfFieldLoader::addAcfFieldgroup(new PostTypeConnection());

/**
 * A banner field group available on posts and terms which can be used to
 * display slideshows in the header.
 */
AcfFieldLoader::addAcfFieldgroup(new HeroComponent());

/**
 * Activate ACF Option Page.
 */
AcfFieldLoader::addAcfFieldgroup(new OptionsPage());

/**
 * Add foundation palette colors for hero overlays.
 */
add_filter('acf/load_field/name=slide_overlay', function ($field) {
    $field['choices'] = ['none' => __('None', 'theme-admin')] + Foundation\palette('overlay');
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
