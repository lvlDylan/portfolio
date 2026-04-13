<?php
require_once __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;

$projectRoot = dirname(__DIR__);

$dotenv = Dotenv::createImmutable($projectRoot);
$dotenv->load();

define('API_SECRET_KEY', $_ENV["API_SECRET_KEY"]);