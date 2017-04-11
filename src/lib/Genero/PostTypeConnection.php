<?php

namespace Genero\Sage;

use Genero\Component\AcfFieldLoader;
use Genero\Component\ComponentInterface;

class PostTypeConnection implements ComponentInterface
{
    private static $connections;
    protected static $groupKey = 'group_5840a5c05f9d2';

    public function init()
    {
        add_filter('acf/init', [$this, 'addAcfFieldgroup']);
    }

    public function addAcfFieldgroup()
    {
        require_once __DIR__ . '/PostTypeConnection/acf.php';
    }

    public function validateRequirements()
    {
        $success = true;
        if (!class_exists('acf_field_post_type_chooser')) {
            add_action('admin_notices', function () {
                // @codingStandardsIgnoreLine
                echo '<div class="error"><p><a href="https://github.com/generoi/acf-post-type-chooser" target="_blank">ACF Post Type Chooser</a> is required for PostTypeConnection feature.</p></div>';
            });
            $success = false;
        }
        return $success;
    }

    public static function postTypeConnections()
    {
        if (!isset(self::$connections)) {
            self::$connections = get_field('post_type_connection', 'option');
            if (!self::$connections) {
                self::$connections = [];
            }
        }
        return self::$connections;
    }

    public static function isParent($pid, $postType)
    {
        foreach (self::postTypeConnections() as $connection) {
            if ($connection['post_type'] != $postType) {
                continue;
            }
            if (in_array($pid, $connection['parent_page'])) {
                return true;
            }
        }
        return false;
    }
}