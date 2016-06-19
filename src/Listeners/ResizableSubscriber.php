<?php

namespace lagbox\resizer\Listeners;

use lagbox\resizer\Resizer;
use lagbox\resizer\Resizable;
use lagbox\resizer\Jobs\ResizeImage;

class ResizableSubscriber
{
    /**
     * \Flashtag\Data\Services\Resizer
     */
    protected $resizer;

    /**
     * @param  \Flashtag\Data\Services\Resizer $resizer
     * @return void
     */
    public function __construct(Resizer $resizer)
    {
        $this->resizer = $resizer;
    }

    public function onCreate(Resizable $model)
    {
        if ($this->resizer->queuIt()) {
            // change to dispatcher class
            dispatch(new ResizeImage($model));
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

        $sizes = array_keys($this->resizer->sizes());

        array_unshift($sizes, 'original');

        foreach ($sizes as $size) {
            $file = $model->{$size};

            if (! $file) { continue; }

            $img = $path .'/'. $file;

            if ($storage->has($img)) {
                $storage->delete($img);
            }
        }
    }

    public function subscribe($events)
    {
        $events->listen(
            'eloquent.deleting: '. Resizable::class,
            self::class .'@onDelete'
        );

        $events->listen(
            'eloquent.created: '. Resizable::class,
            self::class .'@onCreate'
        );
    }
}
