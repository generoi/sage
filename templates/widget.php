<?php

/**
 * If you want your own widget templates, you only need to add a
 * `widget-custom.php` which includes this file. The slug is generated based
 * on the name of the widget.
 */

use Genero\Sage\TimberWidget;

$context['widget'] = new TimberWidget($acfw);
$context['widget']->init(get_defined_vars());

Timber::render([
  'widgets/widget--' . $context['widget']->widget_id . '.twig',
  'widgets/widget--' . strtolower(sanitize_html_class(str_replace(' ', '_', $context['widget']->widget_name))) . '.twig',
  'widgets/widget.twig',
], $context);
