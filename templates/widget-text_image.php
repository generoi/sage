<?php
/**
 * A Text widget using the Section: Text with image fieldgroup.
 *
 * @see Genero\Component\SectionComponent
 * @see src/custom/acf.php
 * @see section/widget--section.twig
 * @see section/section--text_image.twig
 */

$context['section_template'] = 'section--text_image';
include __DIR__ . '/widget-text.php';
