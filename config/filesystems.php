<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => env('FILESYSTEM_CLOUD', 's3'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
            'throw' => true,
            'permissions' => [
                'file' => [
                    'public' => 0644,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0750,
                ],
            ],
        ],

        'local_public' => [
            'driver' => 'local',
            'root' => public_path('uploads/'),
            'url' => env('APP_URL') . '/uploads/',
            'visibility' => 'public',
        ],

        // AWS S3 storage disk for uploaded or generated documents that required authentication.
        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
        ],

        // AWS S3 public disk for public files, such as the application logo.
        's3_public' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID_PUBLIC'),
            'secret' => env('AWS_SECRET_ACCESS_KEY_PUBLIC'),
            'region' => env('AWS_DEFAULT_REGION_PUBLIC'),
            'bucket' => env('AWS_BUCKET_PUBLIC'),
            'url' => env('AWS_URL_PUBLIC'),
        ],

        // Used for temporary file/directory creation. Used for e.g. list/process exports and process import.
        'temp' => [
            'driver' => 'local',
            'root' => storage_path('app/temp'),
            'permissions' => [
                'file' => [
                    'public' => 0644,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0750,
                ],
            ],
        ],

        'logs' => [
            'driver' => 'local',
            'root' => storage_path('logs'),
            'permissions' => [
                'file' => [
                    'public' => 0644,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0750,
                ],
            ],
        ],

        'testing' => [
            'driver' => 'local',
            'root' => storage_path('app/documents/testing'),
            'permissions' => [
                'file' => [
                    'public' => 0644,
                    'private' => 0600,
                ],
                'dir' => [
                    'public' => 0755,
                    'private' => 0750,
                ],
            ],
        ],

        // Local public disk for public files, such as the application logo.
        'testing_public' => [
            'driver' => 'local',
            'root' => public_path('testing'),
            'url' => env('APP_URL') . '/testing',
            'visibility' => 'public',
        ],

    ],

];
