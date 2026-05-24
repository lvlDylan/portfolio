<?php

namespace App\Services;

use PDO;
use PDOException;

/**
 * Class Database
 * * Gère la connexion à la base de données via le design pattern Singleton.
 * Assure qu'une seule instance de connexion PDO est créée et partagée
 * dans toute l'application.
 * * @package App\Services
 */
class Database
{
    /**
     * @var PDO|null L'unique instance de connexion PDO.
     */
    private static ?PDO $instance = null;

    /**
     * Récupère l'instance unique de la connexion à la base de données.
     * * Si l'instance n'existe pas encore, elle est initialisée en utilisant
     * les paramètres définis dans le fichier de configuration.
     * *
     * @return PDO|null L'instance active de la connexion PDO.
     * @throws PDOException
     */
    public static function getInstance(): PDO
    {
        if (self::$instance == null) {
            $config = require ROOT . '/config/database.php';
            $dsn = "mysql:host=" . $config['db_host'] . ";dbname=" . $config['db_name'] . ";charset=utf8mb4";
            self::$instance = new PDO($dsn, $config['db_user'], $config['db_pass'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        }
        return self::$instance;
    }
}