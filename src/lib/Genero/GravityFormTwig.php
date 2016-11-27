<?php

namespace Genero\Sage;

use GFFormDisplay;
use Timber;
use GFFormsModel;

class GravityFormTwig
{
    private $gform_form_args = [];
    private $gform_validation_messages = [];

    public function __construct()
    {
        add_filter('gform_get_form_filter', [$this, 'get_form'], 10, 2);
        add_filter('gform_confirmation', [$this, 'get_confirmation'], 10, 4);
        add_filter('gform_validation_message', [$this, 'save_validation_message'], 10, 2);
        add_filter('gform_form_args', [$this, 'save_form_args'], 10, 1);
        // We inject the button with twig.
        add_filter('gform_submit_button', '__return_false');
    }

    public function get_confirmation($confirmation, $form, $lead, $ajax)
    {
        if ($form['confirmation']['type'] !== 'message') {
            return $confirmation;
        }
        $html = Timber::fetch([
            'forms/form__confirmation--' . sanitize_html_class($form['title']) . '.twig',
            'forms/form__confirmation--' . $form['id'] . '.twig',
            'forms/form__confirmation.twig'
        ], $form);

        if (!$html) {
            return $confirmation;
        }
        return $html;
    }

    /**
     * Render forms with twig.
     */
    public function get_form($form_string, $form)
    {
        extract($this->get_form_args($form['id']));

        if (!isset($form['descriptionPlacement'])) {
            $form['descriptionPlacement'] = 'below';
        }
        if (!isset($form['labelPlacement'])) {
            $form['labelPlacement'] = 'top_label';
        }
        if (!isset($form['subLabelPlacement'])) {
            $form['subLabelPlacement'] = 'below';
        }

        foreach ($form['fields'] as $field) {
            // Inherit form defaults.
            $field->descriptionPlacement = $field->descriptionPlacement ?: $form['descriptionPlacement'];
            $field->labelPlacement = $field->labelPlacement ?: $form['labelPlacement'];
            $field->subLabelPlacement = $field->subLabelPlacement ?: $form['subLabelPlacement'];

            // Retrieve the posted value.
            $this->set_field_value($field, $field_values);

            // Hide some labels.
            switch ($field->type) {
                case 'hidden':
                    $field->labelPlacement = 'hidden_label';
                    $field->visibility = 'hidden';
                    break;
                case 'html':
                case 'section':
                    $field->labelPlacement = 'hidden_label';
                    break;
            }
        }

        $form['ajax'] = $ajax;
        $form['display_title'] = $display_title;
        $form['display_description'] = $display_description;
        $form['action'] = remove_query_arg('gf_token');

        $context = $form;
        $context['form'] = $form;
        $context['form']['original'] = $form_string;
        $context['footer'] = $this->get_gform_footer($form_string, $form);
        $context['footer_after'] = $this->get_gform_iframe($form_string, $context['footer']);
        $context['validation_messages'] = $this->get_validation_messages($form['id']);

        $html = Timber::fetch([
            'forms/form--' . sanitize_html_class($form['title']) . '.twig',
            'forms/form--' . $form['id'] . '.twig',
            'forms/form.twig'
        ], $context);

        if (!$html) {
            return $form_string;
        }
        // Ajax response.
        if (strpos($form_string, "<!DOCTYPE") === 0) {
            return "<!DOCTYPE html><html><head><meta charset='UTF-8' /></head><body class='GF_AJAX_POSTBACK'>$html</body></html>";
        }
        return $html;
    }

    protected function set_field_value($field, $field_values)
    {
        $field->value = GFFormsModel::get_field_value($field, $field_values);

        // Use the POST values as default values so they're saved between
        // validations.
        switch ($field->type) {
            case 'checkbox':
            // case 'multiselect': @todo not working.
            case 'select':
                if (!isset($field->value) || (is_array($field->value) && empty($field->value))) {
                    continue;
                }
                $value = is_array($field->value) ? $field->value : [$field->value];
                foreach ($field->choices as $idx => $choice) {
                    $field->choices[$idx]['isSelected'] = in_array($choice['value'], $value);
                }
                break;
            case 'radio':
                if (!isset($field->value)) {
                    continue;
                }

                $possible_values = array_map(function ($choice) {
                    return $choice['value'];
                }, $field->choices);

                $other_chosen = $field->enableOtherChoice && !in_array($field->value, $possible_values);
                foreach ($field->choices as $idx => $choice) {
                    $field->choices[$idx]['isSelected'] = ($choice['value'] == $field->value);
                    if (!empty($choice['isOtherChoice']) && $other_chosen) {
                        $field->choices[$idx]['isSelected'] = true;
                    }
                }
                break;
            // @todo fileupload
            default:
                if (isset($field->value)) {
                    $field->defaultValue = $field->value;
                }
                break;
        }
        return $field;
    }

    /**
     * Get the hidden fields in the footer.
     */
    protected function get_gform_footer($form_string, $form)
    {
        extract($this->get_form_args($form['id']));

        $footer = GFFormDisplay::gform_footer($form, 'gform_footer ' . $form['labelPlacement'], $ajax, $field_values, '', $display_title, $display_description, $tabindex);
        // Remove tabindex's as they use a static counter.
        $form_string = preg_replace('/tabindex=\'[0-9]+\'/', '', $form_string);
        $footer = preg_replace('/tabindex=\'[0-9]+\'/', '', $footer);
        return $footer;
    }

    protected function get_gform_iframe($form_string, $footer)
    {
        // Iframe is located after the hidden fields.
        $iframe = mb_substr($form_string, mb_strpos($form_string, $footer) + mb_strlen($footer));
        // Remove the ending wrappers and just grab the iframe and js stuff.
        $iframe = preg_replace('|^\s+</form>\s+</div>\s+|', '', $iframe);
        return $iframe;
    }

    public function save_validation_message($message, $form)
    {
        $form_id = $form['id'];
        $this->gform_validation_messages[$form_id][] = strip_tags($message);
        return $message;
    }

    protected function get_validation_messages($form_id)
    {
        return isset($this->gform_validation_messages[$form_id]) ? $this->gform_validation_messages[$form_id] : [];
    }

    /**
    * Store the arguments passed to X function and inject them again in
    * gform_get_form_filter.
    */
    public function save_form_args($args)
    {
        $form_id = $args['form_id'];
        $this->gform_form_args[$form_id] = $args;
        return $args;
    }

    protected function get_form_args($form_id)
    {
        return $this->gform_form_args[$form_id];
    }
}
