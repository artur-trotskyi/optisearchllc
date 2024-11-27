<?php

namespace App\Services\Logger;

use Illuminate\Support\Facades\File;

class FileLogger extends BaseLogger
{
    private string $filePath;

    public function __construct()
    {
        parent::__construct();

        $this->filePath = $this->loggerConfig['file_path'];
    }

    public function send(string $message): void
    {
        $message = $this->processMessage($message);
        File::append($this->filePath, $message.PHP_EOL);
    }

    protected function getLoggerConfigKey(): string
    {
        return 'file';
    }
}
