<?php

namespace lagbox\Resizer;

use lagbox\Resizer\Resizer;
use lagbox\Resizer\Resizable;
use Illuminate\Support\ServiceProvider;
use Intervention\Image\ImageServiceProvider;
use lagbox\Resizer\Listeners\ResizableObserver;

class ResizerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Resizable::$resizer = $this->app['resizer'];

        Resizable::observe($this->app[ResizableObserver::class]);

        $this->publish();
    }

    public function register()
    {
        $this->app->singleton('resizer', function ($app) {
            return new Resizer($app->filesystem, $app->config->get('resizer'));
        });

        $this->app->alias('resizer', Resizer::class);

        $this->app->register(ImageServiceProvider::class);
    }

    protected function publish()
    {
        $this->publishes([
            __DIR__.'/config/resizer.php' => config_path('resizer.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/database/migrations/' => database_path('migrations')
        ], 'migrations');
    }
}
