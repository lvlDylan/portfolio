<?php

namespace App\Services;

use Redis;
use RedisException;

/**
 * Service de gestion de la connexion Redis (Pattern Singleton).
 * * Ce service garantit qu'une seule et unique connexion au serveur Redis
 * est partagée et réutilisée à travers toute l'application.
 * * @package App\Services
 */
class RedisService
{
    /**
     * Instance unique du client Redis.
     * * @var Redis|null
     */
    private static ?Redis $instance = null;

    /**
     * Récupère l'instance unique de connexion à Redis.
     * * Si l'instance n'existe pas encore, elle est initialisée en lisant
     * la configuration globale de l'application.
     * * @return Redis L'instance configurée et connectée du client Redis.
     * @throws RedisException Si la connexion au serveur Redis échoue.
     */
    public static function getInstance(): Redis
    {
        if (self::$instance === null) {
            $config = require ROOT . '/config/database.php';

            // Initialisation et connexion au serveur Redis
            self::$instance = new Redis();
            self::$instance->connect($config["redis_host"], $config["redis_port"]);
        }

        return self::$instance;
    }

}