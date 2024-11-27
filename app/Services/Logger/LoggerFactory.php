<?php

namespace App\Services\Logger;

use InvalidArgumentException;

class LoggerFactory
{
    public static function create(string $type): LoggerInterface
    {
        $loggers = config('logger.loggers');

        if (! isset($loggers[$type])) {
            throw new InvalidArgumentException("Logger type '{$type}' is not supported.");
        }

        return match ($type) {
            'email' => new EmailLogger,
            'database' => new DatabaseLogger,
            'file' => new FileLogger,
            default => throw new InvalidArgumentException("Logger type '{$type}' is not supported."),
        };
    }
}
