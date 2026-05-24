<?php

/**
 * Configuration de la base de données.
 * * @return array{host: string, name: string, user: string, pass: string}
 */
return [
    "db_host" => $_ENV["DB_HOST"],
    "db_name" => $_ENV["DB_NAME"],
    "db_user" => $_ENV["DB_USER"],
    "db_pass" => $_ENV["DB_PASS"],
    "redis_host" => $_ENV["REDIS_HOST"],
    "redis_port" => $_ENV["REDIS_PORT"],
];