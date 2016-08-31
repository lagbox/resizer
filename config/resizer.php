<?php

return [

    // The configuration for using the storage system
    'storage' => [

        // the disk options to use
        'disks' => [
            'public' => [
                'disk' => 'public',
                'path' => 'images',

                // a path from the public folder to help with urls
                'public_path' => 'storage/images',
            ],
        ],

        // the default disk to use
        'default' => 'public',
    ],

    // key value array of the shortname and dimensions for a size
    'sizes' => [
        'lg' => 600,
        'md' => 400,
        'sm' => 200,
        'xs' => 80,
    ],

    // to queue a job for resizing or to do it inline
    'queue' => false,

    // a closure that returns the formatted name of an image for a certain size
    // $filename, $extension, $size will be passed
    /*
    'format' => function ($filename, $extension, $size) {
        return "{$filename}_{$size}.{$extension}";
    },
    */
];
