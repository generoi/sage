<?php
/**
 * A Blockquote widget using the Section: Blockquote fieldgroup.
 *
 * @see Genero\Component\SectionComponent
 * @see src/custom/acf.php
 * @see section/widget--section.twig
 * @see section/section--blockquote.twig
 */

$context['section_template'] = 'section--blockquote';
include __DIR__ . '/widget-text.php';
