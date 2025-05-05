<?php
namespace App\Helpers;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

/**
 * LogHelper provides static methods to log access and error messages
 * to separate log files using Monolog.
 */
class LogHelper
{
    private static function getLogger(string $type): Logger
    {
        $logDir = __DIR__ . '/../../var/logs/';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }

        $file = $type === 'error' ? 'error.log' : 'access.log';
        $logger = new Logger($type);
        $logger->pushHandler(new StreamHandler($logDir . $file, Logger::DEBUG));

        return $logger;
    }

    /**
     * Logs access-related information to access.log.
     *
     * @param array $data Associative array containing method, uri, ip, user, etc.
     * @return void
     */
    public static function logAccess(array $data): void
    {
        $logger = self::getLogger('access');
        $logger->info('Access Log', $data);
    }

    /**
     * Logs errors to error.log.
     *
     * @param string $message Error message.
     * @param array $context Additional context for the error.
     * @return void
     */
    public static function logError(string $message, array $context = []): void
    {
        $logger = self::getLogger('error');
        $logger->error($message, $context);
    }
}
