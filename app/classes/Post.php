<?php

namespace App;

use Timber;
use TimberHelper;;

class Post extends Timber\Post
{
    /**
     * Return related posts.
     */
    public function get_related($posts_per_page = 3)
    {
        $cid = "related_{$this->ID}_{$posts_per_page}";
        if (!isset($this->related)) {
            $this->related = [];
        }

        if (isset($this->related[$cid])) {
            return $this->related[$cid];
        }

        $post = $this;
        $this->related[$cid] = TimberHelper::transient($cid, function () use ($post, $posts_per_page) {
            global $wpdb;
            $terms = $post->terms();
            $tids = implode(',', array_column($terms, 'id'));
            $querystr = "
                SELECT      p.*, COUNT(t.term_id) AS score
                FROM        $wpdb->posts AS p
                INNER JOIN  $wpdb->term_relationships AS tr ON p.ID = tr.object_id
                INNER JOIN  $wpdb->terms AS t ON tr.term_taxonomy_id = t.term_id
                WHERE       p.post_type = '{$post->type}'
                            AND t.term_id IN ({$tids})
                            AND p.ID NOT IN ({$post->ID})
                            AND p.post_status = 'publish'
                GROUP BY    p.ID
                ORDER BY    score DESC
                LIMIT       $posts_per_page
            ";
            $posts = $wpdb->get_results($querystr, OBJECT);
            return (new Timber\PostQuery($posts))->get_posts();
        }, Timber::$cache ? DAY_IN_SECONDS : false);

        return $this->related[$cid];
    }
}
