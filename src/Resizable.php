<?php

namespace lagbox\Resizer;

use Illuminate\Database\Eloquent\Model;

class Resizable extends Model
{
    // The resizer class if you need access to it.
    public static $resizer;

    protected $fillable = [
        'original',
    ];

    protected $casts = [
        'sizes' => 'array',
    ];

    /**
     * The relationship to the type this belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relation\MorphTo
     */
    public function resizeable()
    {
        return $this->morphTo();
    }

    /**
     * Return the filename for the size or null.
     *
     * @param  string $name The size
     * @return string|null
     */
    public function getSize($name, $webpath = false)
    {
        $webpath = $webpath ? static::$resizer->publicPath() : '';

        if (isset($this->sizes[$name])) {
            return $webpath . $this->sizes[$name];
        } elseif ($name == 'original') {
            return $webpath . $this->attributes['original'];
        }

        return null;
    }

    public function getSizes()
    {
        return array_merge(['original'], array_keys($this->sizes));
    }

    /**
     * Set the filename for the size.
     *
     * @param string $size The size
     * @param string $file The filename
     */
    public function setSize($size, $file)
    {
        $sizes = $this->sizes;
        $sizes[$size] = $file;
        $this->sizes = $sizes;
    }

    /**
     * Get the filename for a size or an attribute.
     *
     * @param  string $name
     * @return mixed
     */
    public function __get($name)
    {
        if ($size = $this->getSize($name)) {
            return $size;
        }

        return parent::__get($name);
    }
}
