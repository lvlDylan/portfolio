<?php

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
}