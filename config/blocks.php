<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Autoloaded Gutenberg Blocks
    |--------------------------------------------------------------------------
    |
    | The Gutenberg blocks listed here will be automatically loaded on the
    | request to your application.
    |
    */
    'blocks' => [
        App\Blocks\ContentListing\ContentListing::class,
        App\Blocks\FeaturedListing\FeaturedListing::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Blade directive
    |--------------------------------------------------------------------------
    |
    | Attach an @acfblock blade directive for manually triggering the rendering
    | of a block.
    |
    */
    'directive' => true,
];
