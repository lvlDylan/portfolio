<?php
require_once "../vendor/autoload.php";

use Dotenv\Dotenv;

$projectRoot = dirname(__DIR__);

$dotenv = Dotenv::createImmutable($projectRoot);
$dotenv->load();