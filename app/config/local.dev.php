<?php

declare(strict_types=1);

// Settings for Dev environment

function myCustomErrorHandler(int $errNo, string $errMsg, string $file, int $line)
{
    echo "Error: #[$errNo] occurred in [$file] at line [$line]: [$errMsg] <br>";
}

set_error_handler('myCustomErrorHandler');

return function (array $settings): array {
    // Enable all error reporting for dev environment.
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');

    $settings['error']['display_error_details'] = true;

    // Database configuration
    $settings['db'] = [
        'host'     => 'localhost',
        'port'     => '3306',
        'database' => 'nhl',
        'username' => 'root',
        'password' => '',
        'options'  => [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    ];

    // JWT secret used in AuthController
    $_ENV['JWT_SECRET'] = '1234';

    return $settings;
};
