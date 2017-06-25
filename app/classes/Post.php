<?php

namespace App;

use Timber;
use TimberHelper;;

class Post extends Timber\Post
{
    public $cache_duration = DAY_IN_SECONDS;

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
            if (function_exists('get_crp_posts_id')) {
                return $post->get_related_by_crp($posts_per_page, $args);
            } else {
                return $post->get_related_by_terms($posts_per_page);
            }
        }, Timber::$cache ? $this->cache_duration : false);

        return $this->related[$cid];
    }

    protected function get_related_by_crp($posts_per_page = 3, $args = []) {
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

    protected function get_related_by_terms($posts_per_page) {
        global $wpdb;
        $terms = $this->terms();
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

    protected function generate_cid($prefix, $args = []) {
        return $prefix . '_' . $this->ID . '_' . substr(md5(json_encode($args)), 0, 6);
    }
}
