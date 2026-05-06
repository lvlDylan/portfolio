<?php

namespace App\Services;

class Database
{
    private static $instance = null;

    public static function getInstance()
    {
        if (self::$instance == null) {
            try {
                $config = require_once ROOT . '/config/database.php';
                $dsn = "mysql:host=" . $config['host'] . ";dbname=" . $config['name'] . ";charset=utf8mb4";
                self::$instance = new \PDO($dsn, $config['user'], $config['pass'], [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (\PDOException $e)
            {
                die("Erreur de connexion à la base de donnée.");
            }
        }

        return self::$instance;
    }
}