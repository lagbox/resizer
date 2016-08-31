<?php

namespace lagbox\Resizer;

use lagbox\Resizer\Resizer;
use lagbox\Resizer\Resizable;
use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Bus\Dispatcher;
use Intervention\Image\ImageServiceProvider;
use lagbox\Resizer\Listeners\ResizableObserver;

class ResizerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap anything needed.
     *
     * @return void
     */
    public function boot()
    {
        Resizable::$resizer = $this->app['resizer'];

        Resizable::observe($this->app[ResizableObserver::class]);

        $this->publish();
    }

    /**
     * Register any bindings with the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('resizer', function ($app) {
            return new Resizer(
                $app['filesystem'],
                $app[Dispatcher::class],
                $app['config']->get('resizer', [])
            );
        });

        $this->app->alias('resizer', Resizer::class);

        $this->app->register(ImageServiceProvider::class);
    }

    /**
     * Assets that need publishing.
     *
     * @return void
     */
    protected function publish()
    {
        $this->publishes([
            __DIR__.'/../config/resizer.php' => config_path('resizer.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }
}
