<?php

namespace App\Traits;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Throwable;

trait LoggerTrait
{

    private static ?Logger $logger = null;

    
    private static function getLoggerInstance(): Logger
    {
        if (self::$logger === null) {
            
            self::$logger = new Logger('Cliento');

           
            $logPath = __DIR__ . '/../../logs/app.log';

           
            $logDir = dirname($logPath);
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }

          
            self::$logger->pushHandler(new StreamHandler($logPath, Logger::DEBUG));
        }

        return self::$logger;
    }

   
    public static function logInfo(string $message, array $context = []): void
    {
        self::getLoggerInstance()->info($message, $context);
    }

   
    public static function logWarning(string $message, array $context = []): void
    {
        self::getLoggerInstance()->warning($message, $context);
    }

 
    public static function logError(string|Throwable $error, array $context = []): void
    {
        if ($error instanceof Throwable) {
            $message = $error->getMessage();
            $context['file']  = $error->getFile();
            $context['line']  = $error->getLine();
            $context['trace'] = $error->getTraceAsString();
        } else {
            $message = $error;
        }

        self::getLoggerInstance()->error($message, $context);
    }
}