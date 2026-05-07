<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dump Server Host
    |--------------------------------------------------------------------------
    |
    | The TCP host and port the dump server will listen on. The server collects
    | var-dump output from your application and displays it in the terminal
    | instead of inline in the browser response.
    |
    */

    'host' => env('DUMP_SERVER_HOST', 'tcp://127.0.0.1:9912'),

    /*
    |--------------------------------------------------------------------------
    | Dump Server Enabled
    |--------------------------------------------------------------------------
    |
    | Set this to false to disable the dump server handler entirely. This is
    | useful in environments where you want to suppress all dump output without
    | removing the package.
    |
    */

    'enabled' => env('DUMP_SERVER_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Cloner Limits
    |--------------------------------------------------------------------------
    |
    | Control how deeply the VarCloner traverses nested structures and the
    | maximum number of items it will clone. Increase these values if you
    | need to inspect very large or deeply nested objects.
    |
    */

    'max_depth' => env('DUMP_SERVER_MAX_DEPTH', 10),
    'max_items' => env('DUMP_SERVER_MAX_ITEMS', 2500),

    /*
    |--------------------------------------------------------------------------
    | Log Support
    |--------------------------------------------------------------------------
    |
    | When enabled, every dump call will also be written to the specified log
    | channel at the given level. Useful for capturing dumps in environments
    | where the dump server is not running.
    |
    */

    'log' => [
        'enabled' => env('DUMP_SERVER_LOG_ENABLED', false),
        'channel' => env('DUMP_SERVER_LOG_CHANNEL', 'stack'),
        'level' => env('DUMP_SERVER_LOG_LEVEL', 'debug'),
    ],

];
