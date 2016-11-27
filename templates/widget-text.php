<?php
/**
 * A Text widget using the Section: Text fieldgroup.
 *
 * @see Genero\Component\SectionComponent
 * @see src/custom/acf.php
 * @see section/widget--section.twig
 * @see section/section--text.twig
 */

use Genero\Sage\TimberWidget;

$context['widget'] = new TimberWidget($acfw);
$context['widget']->init(get_defined_vars());
$context['section_template'] = isset($context['section_template']) ? $context['section_template'] : 'section--text';

Timber::render([
  'widgets/widget--' . $context['widget']->widget_id . '.twig',
  'widgets/widget--' . strtolower(sanitize_html_class(str_replace(' ', '_', $context['widget']->widget_name))) . '.twig',
  'widgets/widget--section.twig',
  'widgets/widget.twig',
], $context);
