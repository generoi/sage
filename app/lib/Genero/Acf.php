<?php

namespace Genero\Sage;

class Acf
{
    public static function is_default_language()
    {
        $default_language = self::get_default_language();
        $current_language = self::get_language();
        return $current_language && $current_language === $default_language;
    }

    public static function get_language()
    {
        return acf_get_setting('current_language');
    }

    public static function get_default_language()
    {
        return acf_get_setting('default_language');
    }

    public static function load_value_in_language($post_id, $field, $language)
    {
        $current_language = self::get_language();
        // Switch to the specified language
        acf_update_setting('current_language', $language);

        // Fetch the value as the new language.
        $value = acf_get_metadata($post_id, $field['name']);
        $value = maybe_unserialize($value);

        // Switch back to the original language.
        acf_update_setting('current_language', $current_language);

        return $value;
    }

    public static function is_value_empty($value)
    {
        if (!is_null($value)) {
            if (is_array($value)) {
                // Get from array all the not empty strings
                $is_empty = array_filter($value, function ($value_c) {
                    return $value_c !== '';
                });
                // Not an array of empty values
                if (empty($is_empty)) {
                    return true;
                }
            } else {
                if ($value === '') {
                    return true;
                }
            }
        }
        return false;
    }

    public static function action_collapse_fields()
    {
        ?>
        <script>
            jQuery('.acf-repeater .acf-row, .acf-flexible-content .layout')
                .filter(function () { return $(this).parents('.acf-field-component-field').length < 1; })
                .addClass('-collapsed');
        </script>
        <?php
    }
}
