<?php

namespace App\Blocks\ContentListing;

use Genero\Sage\AcfBlocks\Block;
use Genero\Sage\AcfBlocks\AcfBlocks;
use Genero\Sage\AcfBlocks\Facades\AcfBlock;
use Illuminate\View\View;

class ContentListing extends Block
{
    /** @var array The block registration settings. */
    public static $register = [
        'name' => 'content-listing',
        'title' => 'Content listing',
        'description' => 'A block listing content based on filters',
        'category' => 'sage',
        'align' => 'wide',
        'mode' => 'preview',
        'icon' => 'excerpt-view',
        'keywords' => ['post', 'query'],
        'supports' => [],
        'styles' => [
            'accordion' => 'Accordion',
        ],
    ];

    /**
     * Data to be passed to the rendered block.
     */
    public function with($data, $view)
    {
        $data['fields'] = (object) array_merge([
            'posts_per_page' => 3,
            'order_by' => ['date'],
            'order' => 'DESC',
            'post_type' => 'post',
            'large_columns' => 3,
            'medium_columns' => null,
            'small_columns' => null,
            'category' => null,
            'use_pagination' => false,
        ], $this->block['fields'] ?? get_fields() ?: []);

        $data['posts'] = get_posts($this->query($data['fields']));

        if (!$data['fields']->medium_columns) {
            $data['fields']->medium_columns = ceil($data['fields']->large_columns / 2);
        }
        if (!$data['fields']->small_columns) {
            $data['fields']->small_columns = ceil($data['fields']->large_columns / 4);
        }

        return $data;
    }

    protected function query($data): array
    {
        $query = [
            'posts_per_page' => $data->posts_per_page,
            'orderby' => implode(' ', $data->order_by),
            'order' => $data->order,
            'post_type' => $data->post_type,
            'post_status' => 'publish',
            'paged' => $data->use_pagination ? (get_query_var('paged') ?: 1) : null,
        ];

        $postType = $data->post_type;
        if (is_array($postType)) {
            $postType = reset($postType);
        }

        foreach ($this->getTaxonomyFields($postType) as $field => $taxonomy) {
            if (!empty($data->{$field})) {
                $query['tax_query'][] = [
                    'taxonomy' => $taxonomy,
                    'terms' => $data->{$field},
                ];
            }
        }

        return $query;
    }

    protected function getTaxonomyFields(string $postType): array
    {
        return collect(get_object_taxonomies($postType))
            ->mapWithKeys(function ($taxonomy) {
                return ["tax_$taxonomy" => $taxonomy];
            })
            ->all();
    }

    /**
     * {@inhertiDoc}
     */
    public function render(View $view): string
    {
        if (empty($view->posts)) {
            if ($this->isPreview()) {
                return '<div class="acf-block-placeholder">' . __('No results found...') . '</div>';
            }
            return '';
        }

        return parent::render($view);
    }

}
