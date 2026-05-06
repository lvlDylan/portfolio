<?php

/** @var string Chemin absolu vers la racine du projet
 * @noinspection PhpVarTagWithoutVariableNameInspection
 */
define("ROOT", dirname(__DIR__));

require_once ROOT . "/config/bootstrap.php";

/** Instance principale du routeur */
$router = new App\Router();
$router->run();