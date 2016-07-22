<?php

namespace lagbox\Resizer\Traits;

trait HasResizableImage
{
    protected $resizableName = 'image';

    /**
     * Add a Resizable Image to your model by filename.
     *
     * @param  string $filename
     * @return \lagbox\Resizer\Resizable The created model.
     */
    public function addImage($filename)
    {
        return $this->{$this->resizableName}()->create([
            'original' => $filename,
        ]);
    }

    /**
     * Return the desired filename for a resizable image.
     *
     * @return string The formatted string filename
     */
    public function getResizableName()
    {
        return $this->id .'-'. $this->slug;
    }
}
