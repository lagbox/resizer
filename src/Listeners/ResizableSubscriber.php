<?php

namespace lagbox\Resizer\Listeners;

use lagbox\Resizer\Resizer;
use lagbox\Resizer\Resizable;
use lagbox\Resizer\Jobs\ResizeImage;
use Illuminate\Contracts\Bus\Dispatcher;

class ResizableSubscriber
{
    /**
     * \lagbox\resizer\Resizer
     */
    protected $resizer;

    /**
     * @param  \lagbox\Resizer\Resizer $resizer
     * @param  \Iluminate\Contracts\Bus\Dispatcher $dispatcher
     * @return void
     */
    public function __construct(Resizer $resizer, Dispatcher $dispatcher)
    {
        $this->resizer = $resizer;
        $this->dispatcher = $dispatcher;
    }

    public function onCreate(Resizable $model)
    {
        if ($this->resizer->queuIt()) {
            $this->dispatcher->dispatch(new ResizeImage($model));
        } else {
            $this->resizer->doIt($model);
        }
    }

    public function onDelete(Resizable $model)
    {
        // get the disk instance
        $storage = $this->resizer->getDisk();

        // get the path for the disk
        $path = $this->resizer->getPath();

        // get sizes and add original to it

        $sizes = (array) $model->sizes;

        array_unshift($sizes, $model->original);

        foreach ($sizes as $file) {
            $img = $path .'/'. $file;

            if ($storage->has($img)) {
                $storage->delete($img);
            }
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            'eloquent.deleted: '. Resizable::class,
            self::class .'@onDelete'
        );

        $events->listen(
            'eloquent.created: '. Resizable::class,
            self::class .'@onCreate'
        );
    }
}
