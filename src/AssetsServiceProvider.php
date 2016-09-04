<?php

namespace Chriscubos\Assets;

use Illuminate\Support\Facades\Blade;
use Chriscubos\Packager\ServiceProvider;
use Illuminate\Contracts\Foundation\Application;
use View;

class AssetsServiceProvider extends ServiceProvider
{
    protected $defer = false;

    protected $package_path = __DIR__.'/';

    public function boot()
    {
    }

    public function register()
    {
        $this->publishAll();
        $this->registerBladeDirectives();
        $this->loadAliases();
    }

    public function provides()
    {
        return ['assets'];
    }

    private function registerBladeDirectives()
    {
        Blade::directive('assets', function ($expression) {
            return "<?=Assets::packages($expression);?>";
        });
    }

    private function loadAliases()
    {
        $loader = \Illuminate\Foundation\AliasLoader::getInstance();
        $loader->alias('Assets', 'Chriscubos\Assets\Assets');
    }
}
