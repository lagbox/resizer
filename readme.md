# Simple Image Resizer via Intervention

# Under development

Do not even think about using this yet :).

Just playing with some ideas here.

## Requirements

This is designed for `Laravel >= 5.2`.

## Eloquent Relationship

Add a relationship on your model for the Resizable Image:

```php
public function images()
{
    return $this->morphMany(\lagbox\resizer\Resizable::class);
}
```

Or

```php
public function image()
{
    return $this->morphOne(\lagbox\resizer\Resizable::class);
}
```

### Add an addImage method

```php
public function addImage($filename)
{
    $this->images()->create([
        'original' => $filename,
    ]);
}
```

Or

```php
public function addImage($filename)
{
    $this->image()->create([
        'original' => $filename,
    ]);
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
    $filename = 'something.jpg';

    $resizer->handleUpload($image, $filename);

    $model->addImage($filename);
}
```

## Resizing

Creating a new Resizable Image is all you have to do. The resize will fire off via an event.

## Deleting

Deleting the model will fire an event and cause the images to be deleted from the filesystem.

## Queue

If you want to have the resizing happen in a queue job, you can set `queue` to `true` in the config.