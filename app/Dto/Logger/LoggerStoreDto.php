<?php

namespace App\Dto\Logger;

use App\Traits\MakeableTrait;

final readonly class LoggerStoreDto
{
    use MakeableTrait;

    public string $message;

    /**
     * LoggerStoreDto constructor.
     *
     * @param  array  $data  An associative array with data for store logger.
     */
    public function __construct(array $data)
    {
        $this->message = $data['message'] ?? 'Default log message';
    }
}
