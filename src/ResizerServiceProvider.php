<?php

namespace lagbox\resizer;

use lagbox\resizer\Resizer;
use lagbox\resizer\Resizable;
use Illuminate\Support\ServiceProvider;
use lagbox\resizer\Listener\ResizableSubscriber;

class ResizerServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // register subscriber
        $this->app['events']->subscribe(ResizableSubscriber::class);

        Resizable::$resizer = $this->app['resizer'];
    }

    public function register()
    {
        $this->app->bind('resizer', Resizer::class);

        $this->app->alias('resizer', Resizer::class);
    }
}
