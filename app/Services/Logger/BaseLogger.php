<?php

namespace App\Services\Logger;

use InvalidArgumentException;

abstract class BaseLogger implements LoggerInterface
{
    private string $loggerType;

    protected array $loggerConfig;

    public function __construct()
    {
        $this->loggerConfig = config('logger.loggers.'.$this->getLoggerConfigKey());
        if (empty($this->loggerConfig)) {
            throw new InvalidArgumentException("{$this->getLoggerConfigKey()} logger configuration is missing.");
        }

        $this->setType($this->loggerConfig['type']);
    }

    public function getType(): string
    {
        return $this->loggerType;
    }

    public function setType(string $type): void
    {
        $this->loggerType = $type;
    }

    public function sendByLogger(string $message, string $loggerType): void
    {
        if ($loggerType !== $this->getType()) {
            throw new InvalidArgumentException("Unsupported logger type: {$loggerType}.");
        }

        $message = $this->processMessage($message);
        $this->send($message);
    }

    protected function processMessage(string $message): string
    {
        return '"'.$message.'" was sent via '.$this->getType();
    }

    abstract protected function getLoggerConfigKey(): string;
}
