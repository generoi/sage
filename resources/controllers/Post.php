<?php

/**
 * @file
 * Contains App\Controller\Post class used as a default extension of
 * Timber\Post.
 */

namespace App\Controller;

use App;
use Timber;
use TimberHelper;

class Post extends Timber\Post
{
    /** @var int The duration any template cache should stay fresh */
    public $cache_duration = DAY_IN_SECONDS;

    /**
     * Disable expensive get_post_class() funciton call which we do not use.
     */
    public function post_class($class = '')
    {
        return $class;
    }

    /**
     * Return related posts.
     */
    public function get_related($posts_per_page = 3, $args = [])
    {
        $cid = $this->generate_cid('related', func_get_args());
        if (!isset($this->related)) {
            $this->related = [];
        }

        if (isset($this->related[$cid])) {
            return $this->related[$cid];
        }

        $post = $this;
        $this->related[$cid] = TimberHelper::transient($cid, function () use ($post, $posts_per_page, $args) {
            if ($this->is_crp_active()) {
                return $post->get_related_by_crp($posts_per_page, $args);
            } else {
                return $post->get_related_by_terms($posts_per_page);
            }
        }, App\config('timber.cache') ? $this->cache_duration : false);

        return $this->related[$cid];
    }

    /**
     * Return related posts based on Contextual Related Posts result.
     */
    protected function get_related_by_crp($posts_per_page = 3, $args = [])
    {
        $related = get_crp_posts_id(array_merge($args, [
            'post_id' => $this->ID,
            'limit' => $posts_per_page,
        ]));
        if (empty($related)) {
            return [];
        }
        $related = wp_list_pluck($related, 'ID');
        return (new Timber\PostQuery($related))->get_posts();
    }

    /**
     * Return related posts based on terms.
     */
    protected function get_related_by_terms($posts_per_page = 3)
    {
        global $wpdb;
        $terms = $this->terms();
        if (!empty($terms))
        {
            $tids = implode(',', array_column($terms, 'id'));
            $querystr = "
                SELECT      p.*, COUNT(t.term_id) AS score
                FROM        $wpdb->posts AS p
                INNER JOIN  $wpdb->term_relationships AS tr ON p.ID = tr.object_id
                INNER JOIN  $wpdb->terms AS t ON tr.term_taxonomy_id = t.term_id
                WHERE       p.post_type = '{$this->type}'
                            AND t.term_id IN ({$tids})
                            AND p.ID NOT IN ({$this->ID})
                            AND p.post_status = 'publish'
                GROUP BY    p.ID
                ORDER BY    score DESC
                LIMIT       $posts_per_page
            ";
            $posts = $wpdb->get_results($querystr, OBJECT);
            return (new Timber\PostQuery($posts))->get_posts();
        }
    }

    /**
     * Check if post has Contextual Related Post functionality active.
     */
    protected function is_crp_active()
    {
        if (!function_exists('get_crp_posts_id')) {
            return false;
        }
        // @see https://github.com/WebberZone/contextual-related-posts/blob/ec1ec84df057dca5f1b61695fd450776cc181dbe/includes/content.php#L57
        global $crp_settings;
        if (!empty($crp_settings['exclude_on_post_types']) && strpos($crp_settings['exclude_on_post_types'], '=') === false) {
            $exclude_on_post_types = explode(',', $crp_settings['exclude_on_post_types']);
        } else {
            parse_str($crp_settings['exclude_on_post_types'], $exclude_on_post_types);
        }
        return in_array($this->type, $exclude_on_post_types);
    }

    /**
     * Generate a unique cahe id based on a prefix and arguments.
     */
    protected function generate_cid($prefix, $args = [])
    {
        return $prefix . '_' . $this->ID . '_' . substr(md5(json_encode($args)), 0, 6);
    }
}
