<?php

namespace lagbox\Resizer;

use Illuminate\Support\Arr;
use lagbox\Resizer\Resizable;
use Illuminate\Support\Facades\App;
use lagbox\Resizer\Jobs\ResizeImage;
use Intervention\Image\Facades\Image;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Contracts\Config\Repository as Config;
use Illuminate\Contracts\Filesystem\Factory as Storage;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Resizer
{
    protected $path;

    protected $disk;

    protected $queueIt;

    protected $formatter;

    public static $sizes = [
        'lg' => 600,
        'md' => 400,
        'sm' => 200,
        'xs' => 80,
    ];

    /**
     * @param  \Illuminate\Contracts\Filesystem\Factory $storage
     * @param  array $config
     * @return void
     */
    public function __construct(Storage $storage, $config = [])
    {
        $disk = Arr::get(
            $config,
            'storage.disks.'. $config['storage']['default'],
            'public'
        );

        $this->disk = $storage->disk($disk['disk']);

        $this->path = Arr::get($disk, 'path', 'images');

        $this->publicPath = Arr::get($disk, 'public_path', 'storage/images');

        $this->queueIt = Arr::get($config, 'queue', false);

        $this->formatter = Arr::get($config, 'format');

        if (isset($config['sizes'])) {
            static::sizes($config['sizes']);
        }
    }

    /**
     * Get or set the sizes.
     *
     * @param  array|null $sizes
     * @return array
     */
    public static function sizes($sizes = null)
    {
        return $sizes ? static::$sizes = $sizes : static::$sizes;
    }

    /**
     * Get the Filesystem disk.
     *
     * @return \Illuminate\Filesystem\FilesystemAdapter
     */
    public function disk()
    {
        return $this->disk;
    }

    /**
     * Get the path for use with the disk.
     *
     * @return string
     */
    public function path()
    {
        return $this->path;
    }

    public function publicPath()
    {
        return rtrim($this->publicPath, '/') .'/';
    }

    /**
     * Use of queue for resizing.
     *
     * @return bool
     */
    public function queueIt()
    {
        return $this->queueIt;
    }

    /**
     * Do the resizing for the Resizable images sizes
     *
     * @param  \lagbox\Resizer\Resizable $entity
     * @return void
     */
    public function resizing(Resizable $model)
    {
        if ($this->queueIt()) {
            $dispatcher = App::make(Dispatcher::class)
                ->dispatch(new ResizeImage($model));
        } else {
            $this->doIt($model);
        }
    }

    /**
     * Do the resizing for the Resizable images sizes
     *
     * @param  \lagbox\Resizer\Resizable $entity
     * @return void
     */
    public function doIt(Resizable $entity)
    {
        $file = $entity->original;

        $image = Image::make($this->disk->get($this->path .'/'. $file));

        $filename = $file;

        foreach ($this->formatSizes() as $size => $dems) {
            if ($this->resize($image, $dems)) {
                // get a new filename and save the resized image
                $filename = $this->formatFileName($file, $size);
                $this->save($image, $filename);
            }

            $entity->setSize($size, $filename);
        }

        $entity->save();
    }

    /**
     * Resize our image to defined dimensions
     *
     * @param  \Intervention\Image\Image $img
     * @param  array $dems  the dimensions
     * @return bool
     */
    public function resize($img, $dems)
    {
        // check if there is a need to resize based on dimensions passed
        if ($img->height() < $dems['height'] && $img->width() < $dems['width']) {
            return false;
        }

        // resize to dimensions
        $img->resize($dems['width'], $dems['height'], function ($con) {
            //  respect aspectRatio and never upsize
            $con->aspectRatio();
            $con->upsize();
        });

        return true;
    }

    /**
     * Save the Image to disk.
     *
     * @param  \Intervention\Image\Image $img
     * @param  string $name
     * @param  string|null $path
     * @return void
     */
    public function save($img, $name, $path = null)
    {
        $path = $path ?: $this->path;

        $this->disk->put($path .'/'. $name, $img->stream());
    }


    /**
     * Handle the upload of a file.
     *
     * @param  \Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param  string|null $filename
     * @return string
     */
    public function handleUpload(UploadedFile $file, $filename = null)
    {
        if (is_null($filename)) {
            if (method_exists($file, 'hashName')) {
                $filename = $file->hashName();
            } else {
                $filename = md5_file($file->path()).'.'.$file->extension();
            }
        } elseif (pathinfo($filename, PATHINFO_EXTENSION) == '') {
            $filename .= '.'. $file->extension();
        }

        $this->disk->put(
            $this->path .'/'. $filename,
            file_get_contents($file->getRealPath())
        );

        return $filename;
    }

    /**
     * Format the sizes to a standard format.
     *
     * @return array
     */
    protected function formatSizes()
    {
        /*
        allow for multiple formats
            'lg' => 800
        or
            'lg' => [800, 600]
        or
            'lg' => ['width' => ..., 'height' => ...]
         */
        foreach (static::sizes() as $key => $size) {
            if (! is_array($size)) {
                $f[$key] = ['width' => $size, 'height' => $size];
            } elseif (! isset($size['width'])) {
                $f[$key] = ['width' => $size[0], 'height' => $size[1]];
            } else {
                $f[$key] = $size;
            }
        }

        return $f;
    }

    /**
     * Format the filename for the entity and size.
     *
     * @param  string $original Original filename
     * @param  string $size
     * @return string The formatted filename
     */
    protected function formatFileName($original, $size)
    {
        $extension = pathinfo($original, PATHINFO_EXTENSION);
        $filename = pathinfo($original, PATHINFO_FILENAME);

        if ($this->formatter) {
            return call_user_func_array($this->formatter, [$filename, $extension, $size]);
        }

        return "{$filename}_{$size}.{$extension}";
    }
}
