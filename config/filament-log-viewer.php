<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Log File Size
    |--------------------------------------------------------------------------
    |
    | The maximum size (in kilobytes) for a single log file to be read.
    | Files larger than this will be skipped to prevent memory exhaustion.
    */
    'max_log_file_size' => (int) env('LOG_MAX_SIZE_KB', 2048),
    'enable_delete' => (bool) env('LOG_ENABLE_DELETE', true),
];
