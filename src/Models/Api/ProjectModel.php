<?php

namespace App\Models\Api;

use App\Services\Database;
use PDO;

class

ProjectModel
{
    private ?PDO $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * @return array|false array{
     * array{
     * id: int,
     * title: string,
     * description: string,
     * description_shortened:string,
     * image_path: string,
     * github_link: string,
     * created_at: string
     * }
     * Liste des projets.
     */
    public function findAll(): array | false
    {
        return $this->database->query("SELECT * FROM projects")->fetchAll(PDO::FETCH_ASSOC);
    }
}