<?php

namespace lagbox\Resizer\Listeners;

use lagbox\Resizer\Resizer;
use lagbox\Resizer\Jobs\ResizeImage;

class ResizableObserver
{
    /**
     * @param  \lagbox\Resizer\Resizer $resizer
     */
    public function __construct(Resizer $resizer)
    {
        $this->resizer = $resizer;
    }

    /**
     * @param  \lagbox\Resizer\Resizable $model
     * @return void
     */
    public function deleted($model)
    {
        $this->resizer->delete($model);
    }

    /**
     * @param  \lagbox\Resizer\Resizable $model
     * @return void
     */
    public function creating($model)
    {
        if ($model->sizes == null) {
            $model->sizes = [];
        }
    }

    /**
     * @param  \lagbox\Resizer\Resizable $model
     * @return void
     */
    public function created($model)
    {
        $this->resizer->resizing($model);
    }
}
