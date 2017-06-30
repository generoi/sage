<?php

namespace Genero\Sage;

use Genero\Component\AcfFieldLoader;
use Genero\Component\ComponentInterface;

class OptionsPage implements ComponentInterface
{
    protected static $groupKey = 'group_5841faa52ddd4';

    public function __construct()
    {
        if ($this->validateRequirements()) {
            add_filter('acf/init', 'acf_add_options_page');
            add_filter('timber/context', function ($context) {
                if (function_exists('get_fields')) {
                    $context['options'] = get_fields('option');
                }
                return $context;
            }, 9);
        }
    }

    public function addAcfFieldgroup()
    {
        if (!_acf_get_field_group_by_key(self::$groupKey)) {
            $json = __DIR__ . '/OptionsPage/optionspage-acf.json';
            AcfFieldLoader::importFieldGroups($json, [self::$groupKey]);
        }
    }

    public function validateRequirements()
    {
        return function_exists('get_fields');
    }
}