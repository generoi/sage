<?php

/**
 * @file
 * Contains App\Controller\Image class used as an extension of
 * TimberExtended\Image.
 */

namespace App\Controller;

use Timber;
use TimberExtended;

class Image extends TimberExtended\Image
{
    /** @inheritdoc */
    public $tojpg = true;
}
