<?php

namespace Genero\Sage;

use TimberExtended;
use WidgetOptionsExtended;

/**
 * Extend the Timber Widget with widget-options integrations.
 */
class TimberWidget extends TimberExtended\Widget
{
    public function init($info, $force = false)
    {
        parent::init($info, $force);

        if (isset($this->{'extended_widget_opts-' . $this->widget_id})) {
            $this->extended_widget_opts = $this->{'extended_widget_opts-' . $this->widget_id};
        }

        if (!empty($this->extended_widget_opts)) {
            $this->widget_options($this->extended_widget_opts);
        }
    }

    // @codingStandardsIgnoreLine
    protected function widget_options($options)
    {
        $extra_classes = WidgetOptionsExtended::get_widget_classes($options);
        foreach ($extra_classes as $class) {
            $this->add_class($class);
        }

        if (!empty($options['class']['title'])) {
            $this->hide_title = true;
        }
        if (!empty($options['class']['id'])) {
            $this->id = $options['class']['id'];
        }
    }
}
