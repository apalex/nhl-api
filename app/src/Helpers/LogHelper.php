<?php

namespace App\Helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * Class LogHelper
 *
 * A utility class for handling application logging using Monolog.
 * Provides static methods to log access and error messages to separate log files.
 *
 * Log files are stored in: [project_root]/var/logs/
 */
class LogHelper
{
    /**
     * Returns a configured Monolog Logger instance.
     *
     * @param string $type The type of logger ('access' or 'error').
     * @return Logger The configured logger instance.
     */
    private static function getLogger(string $type): Logger
    {
        // Construct a general path to the logs directory
        $logPath = dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . 'var' . DIRECTORY_SEPARATOR . 'logs' . DIRECTORY_SEPARATOR;

        // Create a logger instance for the given type
        $logger = new Logger($type);

        // Choose the correct file name based on log type
        $file = $type === 'access' ? 'access.log' : 'error.log';

        // Push a handler for writing to the file (DEBUG level captures all log levels)
        $logger->pushHandler(new StreamHandler($logPath . $file, Logger::DEBUG));

        return $logger;
    }

    /**
     * Logs access-related events to access.log.
     *
     * @param string $message A brief message describing the event.
     * @param array $context Optional additional details: method, URI, IP, user, query parameters, etc.
     * @return void
     */
    public static function logAccess(string $message, array $context = []): void
    {
        self::getLogger('access')->info($message, $context);
    }

    /**
     * Logs error-related events to error.log.
     *
     * @param string $message The error message.
     * @param array $context Optional context such as file, line, exception stack trace, etc.
     * @return void
     */
    public static function logError(string $message, array $context = []): void
    {
        self::getLogger('error')->error($message, $context);
    }
}
