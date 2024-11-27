<?php

namespace App\Services\Logger;

use Exception;
use Illuminate\Support\Facades\Mail;

class EmailLogger extends BaseLogger
{
    private string $recipient;

    public function __construct()
    {
        parent::__construct();

        $this->recipient = $this->loggerConfig['recipient'];
    }

    public function send(string $message): void
    {
        $message = $this->processMessage($message);
        try {
            Mail::raw($message, function ($mail) {
                $mail->to($this->recipient)
                    ->subject('Logger Message');
            });
        } catch (Exception) {
            return;
        }
    }

    protected function getLoggerConfigKey(): string
    {
        return 'email';
    }
}
