<?php

namespace App\Services\Logger;

use App\Models\Logger;

class DatabaseLogger extends BaseLogger
{
    public function __construct()
    {
        parent::__construct();
    }

    public function send(string $message): void
    {
        $message = $this->processMessage($message);
        Logger::create([
            'message' => $message,
        ]);
    }

    protected function getLoggerConfigKey(): string
    {
        return 'database';
    }
}
