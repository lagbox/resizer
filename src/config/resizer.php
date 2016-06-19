<?php

return [
    'storage' => [
        'disks' => [
            'public' => [
                'disk' => 'public',
                'path' => 'images',
                'public_path' => 'storage/images',
            ],
        ],
        'default' => 'public',
    ],
    'sizes' = [
        'lg' => 600,
        'md' => 400,
        'sm' => 200,
        'xs' => 80,
    ],
    'queue' => false,
];
