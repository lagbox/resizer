# Simple Image Resizer via Intervention

# Under development

Do not even think about using this yet :).

Just playing with some ideas here.

## Requirements

This is designed for `Laravel >= 5.1`.

## Install

Composer require that bad boy.

    composer require lagbox/laravel-resizer

Add the Service Provider to `config/app.php`.

```php
...
    'providers' => [
        ...
        lagbox\Resizer\ResizerServiceProvider::class,
    ],
...
```

Publish the config file and migration.

    artisan vendor:publish --provider="lagbox\Resizer\ResizerServiceProvider"

Run the migration.

    artisan migrate

If you want to make any configuration adjustments you can edit the config file `config/resizer.php`.

Make sure you have the 'public' disk that is setup in `config/app/filesystems.php`. Link `storage/app/public` to `public/storage` and create a folder for your images in there `images`. Make sure that directory has the correct permissions and you should be good to go. (You can adjust the disk and the path in the `resizer.php` config file.)

## Eloquent Relationship

You can add a `morphMany` or `morphOne` relationship on your model that will have the Resizable Image(s):

```php
public function image()
{
    return $this->morphOne(\lagbox\resizer\Resizable::class);
}
```

## Upload

The Resizer can handle the upload for you. You just need to pass it the `UploadedFile` object you get from the request.

```php

public function store(Request $request, Resizer $resizer)
{
    $image = $request->file('image');

    $filename = $resizer->handleUpload($image);

    // or if you want to specify the filename yourself

    $image = $request->file('image');

    $filename = $resizer->handleUpload($image, 'something.jpg');
    ...
}

```

## Trait

You can use `lagbox\Resizer\Traits\HasResizableImage` trait if you would like a `addImage($filename)` method and a `getResizableName()` method. By default the `getResizableName` method is simply:

    return $this->id .'-'. $this->slug;

You can override this method on your model to change the format of the filename used for the Resizable Image associated with your model.

If you have named your relationship method something other than `image` you can set a parameter on your model to adjust for that.

    protected $resizableName = 'images';

    // or

    protected $resizableName = 'photo';

### Upload and Trait methods

These methods can be used to help you with the upload and saving process.

    public function store(Request $request, Resizer $resizer)
    {
        $post = Post::create($request->all());

        $file = $request->file('image');

        $filename = $resizer->handleUpload($file, $post->getResizableName());

        $image = $post->addImage($filename);
    }


## Resizing

Creating a new Resizable Image is all you have to do. The resize will fire off via an event.

## Deleting

Deleting the model will fire an event and cause the images to be deleted from the filesystem.

## Queue

If you want to have the resizing happen in a queue job, you can set `queue` to `true` in the config.