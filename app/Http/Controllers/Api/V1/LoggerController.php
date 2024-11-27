<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Logger\LoggerStoreRequest;
use App\Services\Logger\LoggerFactory;
use Illuminate\Http\JsonResponse;

class LoggerController extends Controller
{
    private array $config;

    public function __construct()
    {
        $this->config = config('logger');
    }

    /**
     * Sends a log message to the default logger.
     */
    public function log(LoggerStoreRequest $request): JsonResponse
    {
        $message = $request->getDto()->message;
        $loggerType = $this->config['default'];
        $logger = LoggerFactory::create($loggerType);

        $logger->send($message);

        return response()->json(['message' => "Logger sent to {$loggerType}"]);
    }

    /**
     * Sends a log message to a special logger.
     */
    public function logTo(LoggerStoreRequest $request, string $type): JsonResponse
    {
        $message = $request->getDto()->message;
        $logger = LoggerFactory::create($type);

        $logger->sendByLogger($message, $type);

        return response()->json(['message' => "Logger sent to {$type}"]);
    }

    /**
     * Sends a log message to all loggers.
     */
    public function logToAll(LoggerStoreRequest $request): JsonResponse
    {
        $message = $request->getDto()->message;
        $loggers = $this->config['loggers'];

        foreach ($loggers as $type => $config) {
            $logger = LoggerFactory::create($type);
            $logger->send($message);
        }

        return response()->json(['message' => 'Logger sent to all loggers']);
    }
}
