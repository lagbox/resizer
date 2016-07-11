<?php

namespace lagbox\Resizer;

use lagbox\Resizer\Resizer;
use lagbox\Resizer\Resizable;
use Illuminate\Support\ServiceProvider;
use lagbox\Resizer\Listener\ResizableSubscriber;

class ResizerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app['events']->subscribe(ResizableSubscriber::class);

        Resizable::$resizer = $this->app['resizer'];

        $this->publishes();
    }

    public function register()
    {
        $this->app->bind('resizer', Resizer::class);

        $this->app->alias('resizer', Resizer::class);
    }

    protected function publishes()
    {
        $this->publishes([
            __DIR__.'/../config/resizer.php' => config_path('resizer.php')
        ], 'config');

        $this->publishes([
            __DIR__.'/../database/migrations/' => database_path('migrations')
        ], 'migrations');
    }
}
