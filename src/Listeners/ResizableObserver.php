<?php

namespace lagbox\Resizer\Listeners;

use lagbox\Resizer\Resizer;
use lagbox\Resizer\Jobs\ResizeImage;

class ResizableObserver
{
    /**
     * @param \lagbox\Resizer\Resizer $resizer
     */
    public function __construct(Resizer $resizer)
    {
        $this->resizer = $resizer;
    }

    /**
     * @param  lagbox\Resizer\Resizable $model
     * @return void
     */
    public function deleted($model)
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

    /**
     * @param  lagbox\Resizer\Resizable $model
     * @return void
     */
    public function creating($model)
    {
        if ($model->sizes == null) {
            $model->sizes = [];
        }
    }

    /**
     * @param  lagbox\Resizer\Resizable $model
     * @return void
     */
    public function created($model)
    {
        $this->resizer->resizing($model);
    }
}
