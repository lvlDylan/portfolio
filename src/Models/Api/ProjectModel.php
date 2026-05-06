<?php

namespace App\Models\Api;

use App\Services\Database;

class

ProjectModel
{
    private ?\PDO $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * @return array{id: int, title: string, description: string, description_shortened: string, image_path: string, github_link: string, created_at: string}|false
     */
    public function findAll(): array | false
    {
        return $this->database->query("SELECT * FROM projects")->fetchAll();
    }
}