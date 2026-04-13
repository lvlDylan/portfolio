<?php

namespace Dylan\Api;

class Logger
{

    private static string $path = __DIR__ . "/../../logs/";

    public static function log($level, $sender, $message) {
        switch ($level) {
            case "error":
                self::error($sender, $message);
                break;
            default:
                self::info($sender, $message);
        }
    }

    private static function error($sender, $message): void
    {
        $date = date("Y-m-d H:i:s");
        $message = "[$date] TENTATIVE POST PROJET : $sender -- $message\n";
        file_put_contents(self::$path . "error.log", $message, FILE_APPEND);
    }

    private static function info($sender, $message): void
    {
        $date = date("Y-m-d H:i:s");
        $message = "[$date] POST PROJET : $sender -- $message\n";
        file_put_contents(self::$path . "log.log", $message, FILE_APPEND);
    }
}