<?php

namespace App;

use Timber;
use TimberHelper;;

class Post extends Timber\Post {
    /**
     * Return related posts.
     */
    public function get_related($posts_per_page = 3) {
        $cid = "related_{$this->ID}_{$posts_per_page}";
        if (!isset($this->related)) {
            $this->related = [];
        }

        if (isset($this->related[$cid])) {
            return $this->related[$cid];
        }

        $post = $this;
        $this->related[$cid] = TimberHelper::transient($cid, function () use ($post, $posts_per_page) {
            $terms = $post->terms('category');
            $tids = wp_list_pluck($terms, 'ID');
            return (new Timber\PostQuery([
                'category__in' => $tids,
                'post_type' => $post->post_type,
                'post__not_in' => [$post->ID],
                'posts_per_page' => $posts_per_page,
                'ignore_sticky_posts' => true,
                'orderby' => 'rand',
            ]))->get_posts();
        }, Timber::$cache ? DAY_IN_SECONDS : false);

        return $this->related[$cid];
    }
}
