<?php

namespace App\Models\Api;

use App\Services\Database;
use PDO;

/**
 * Class ProjectModel
 * * Gère l'accès aux données de la table 'projects' pour les réponses API.
 * Utilise le service Database pour interagir avec la base de données via PDO.
 * * @package App\Models\Api
 */
class ProjectModel
{
    /**
     * @var PDO|null Instance de connexion à la base de données.
     */
    private ?PDO $database;

    /**
     * ProjectModel constructor.
     * Initialise la connexion à la base de données via le Singleton Database.
     */
    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * Récupère l'ensemble des projets présents en base de données.
     * * @return array<int, array{
     * id: int,
     * title: string,
     * description: string,
     * description_shortened: string,
     * image_path: string,
     * github_link: string,
     * created_at: string
     * }>|false Retourne un tableau associatif des projets ou false en cas d'échec.
     */
    public function findAll(): array | false
    {
        return $this->database->query("SELECT * FROM projects")->fetchAll(PDO::FETCH_ASSOC);
    }
}