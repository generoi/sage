<?php

namespace App\Providers;

use ApprovedDigital\LaravelTailwindConfig\LaravelTailwindConfigServiceProvider as BaseProvider;

class LaravelTailwindConfigServiceProvider extends BaseProvider
{
    /**
     * {@inheritDoc}
     */
    public function register()
    {
        $this->registerTailwindService();

        // @todo if Acorn registers globals before register providers we can
        // remove this overload
    }
}
