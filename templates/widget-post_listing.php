<?php
/**
 * A Post listing widget using the Section: Post listing fieldgroup.
 *
 * @see Genero\Component\SectionComponent
 * @see src/custom/acf.php
 * @see section/widget--section.twig
 * @see section/section--post_listing.twig
 */

$context['section_template'] = 'section--post_listing';
include __DIR__ . '/widget-text.php';
