<?php

namespace App\Blocks\FeaturedListing;

use App\Blocks\ContentListing\ContentListing;
use Genero\Sage\AcfBlocks\Block;
use Genero\Sage\AcfBlocks\AcfBlocks;
use Genero\Sage\AcfBlocks\Facades\AcfBlock;
use Illuminate\View\View;

class FeaturedListing extends ContentListing
{
    public static function register(): array
    {
        $register = parent::register();
        $register['name'] = 'featured-listing';
        $register['title'] = 'Featured listing';
        $register['description'] = 'A block listing featured content picked manually';
        return $register;
    }

    /**
     * Data to be passed to the rendered block.
     */
    public function with($data, $view)
    {
        $data['fields'] = (object) array_merge([
            'large_columns' => 3,
            'medium_columns' => null,
            'small_columns' => null,
            'posts' => [],
        ], $this->block['fields'] ?? get_fields() ?: []);

        $data['posts'] = $data['fields']->posts ?: [];

        if (!$data['fields']->medium_columns) {
            $data['fields']->medium_columns = ceil($data['fields']->large_columns / 2);
        }
        if (!$data['fields']->small_columns) {
            $data['fields']->small_columns = ceil($data['fields']->large_columns / 4);
        }

        return $data;
    }

    /**
     * Data to be passed to the rendered block.
     */
    public function override($data, $view)
    {
        return [
            'classes' => $data['classes'] . ' acf-content-listing',
        ];
    }

    /**
     * {@inhertiDoc}
     */
    public function views(): array
    {
        return [
            "ContentListing::content-listing-{$this->style}",
            "ContentListing::content-listing",
        ];
    }
}
