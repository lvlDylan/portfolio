<?php

namespace App\Services;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Monolog\Processor\WebProcessor;

class LoggerService
{

    private static ?Logger $logger;

    public static function getLogger(): Logger
    {
        if (self::$logger == null) {
            self::$logger = new Logger("portfolio");

            self::$logger->pushProcessor(new WebProcessor(null, [
                'url'         => 'REQUEST_URI',
                'ip'          => 'REMOTE_ADDR',
                'http_method' => 'REQUEST_METHOD'
            ]));

            self::$logger->pushHandler(new StreamHandler(ROOT . "/logs/" . date("Y-m-d") . "-dev.log", Level::Debug));
            self::$logger->pushHandler(new StreamHandler(ROOT . "/logs/" . date("Y-m-d") . "-error.log", Level::Error));
        }

        return self::$logger;
    }

}