<?php

namespace App;

use Genero\Component\AcfFieldLoader;
use Genero\Component\HeroComponent;
use Genero\Component\SectionComponent;

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
 * A flexible content field group to create different type of sections in
 * content. Also register the ACF Widget for the fieldgroup.
 */
AcfFieldLoader::addAcfFieldgroup(new SectionComponent());

/**
 * Activate ACF Option Page.
 */
AcfFieldLoader::addAcfFieldgroup(new OptionsPage());

/**
 * Add foundation palette colors for section background color fields.
 */
add_filter('acf/load_field/name=background_color', function ($field) {
    $field['choices'] = [
        'primary' => __('Primary color', 'theme'),
        'secondary' => __('Secondary color', 'theme'),
        'white' => __('White', 'theme'),
        'black' => __('Black', 'theme'),
    ];
    return $field;
});

/**
 * Add foundation palette colors for section overlay color fields.
 */
add_filter('acf/load_field/name=background_overlay', function ($field) {
    $field['choices'] = [
        'white' => __('Light', 'theme'),
        'black' => __('Dark', 'theme'),
    ];
    return $field;
});

/**
 * Add foundation palette colors for hero overlays.
 */
add_filter('acf/load_field/name=slide_overlay', function ($field) {
    $field['choices'] = [
        'white' => __('Light', 'theme'),
        'black' => __('Dark', 'theme'),
    ];
    return $field;
});

/**
 * Collapse ACF fields by default.
 */
add_action('acf/input/admin_footer', ['Genero\\Sage\\Acf', 'action_collapse_fields']);
