<?php

namespace Src\Utils\Logging;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class AppLogger
{
    private static $logger = null;

    public static function getLogger(): Logger
    {
        if (self::$logger === null) {
            $file_path = PROJECT_ROOT . '/logs/daily_lichess.log';
            self::$logger = new Logger('appName');
            self::$logger->pushHandler(new StreamHandler($file_path, Level::Debug));
        }
        return self::$logger;
    }
}
