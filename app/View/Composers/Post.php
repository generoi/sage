<?php

namespace App\View\Composers;

use App\View\Composers\Concerns\HasPostData;
use Roots\Acorn\View\Composer;

class Post extends Composer
{
    use HasPostData;

    /**
     * List of views served by this composer.
     *
     * @var array
     */
    protected static $views = [
        'index',
        'partials.page-header',
        'partials.content',
        'partials.content-*',
    ];

    /**
     * Data to be passed to view before rendering, but after merging.
     *
     * @return array
     */
    public function override()
    {
        return [
            'title' => $this->title(),
            'post' => $this->post(),
            'content' => $this->content(),
            'printPageHeading' => $this->printPageHeading(),
        ];
    }

    /**
     * Do not print page heading if there's an <h1> tag in the content.
     */
    public function printPageHeading(): bool
    {
        return strpos(get_the_content(), '</h1>') === false;
    }
}
