<?php

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

require_once "../vendor/autoload.php";

/** Appelle la librairie phpdotenv, et charge le fichier .env  */
$dotenv = Dotenv\Dotenv::createImmutable(ROOT);
$dotenv->load();

date_default_timezone_set("Europe/Paris");


/**
 * Si APP_ENV dans le .env est en development, afficher les erreurs.
 */
if ($_ENV["APP_ENV"] == "development") {
    error_reporting(E_ALL);
    ini_set("display_errors", 1);

    $logger = new Logger("portfolio");
    $logger->pushHandler(new StreamHandler(ROOT . "/logs/" . date("Y-m-d") . ".log", Level::Debug));
} else if ($_ENV["APP_ENV"] == "production") {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', '0');
    ini_set('log_errors', 1);
    ini_set('error_log', ROOT . '/logs/php_errors.log');
}