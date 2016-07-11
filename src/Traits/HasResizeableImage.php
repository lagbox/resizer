<?php

namespace lagbox\Resizer\Traits;

trait HasResizableImage
{
    public function getImageSize($name)
    {
        if ($image = $this->image) {
            return $image->getSize($name);
        }
    }
}
