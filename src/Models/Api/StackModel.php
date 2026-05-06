<?php

namespace App\Models\Api;

use App\Services\Database;

class StackModel
{
    private ?\PDO $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * @return array{id: int, name: string, category: string, icon_name: string, color_name: string}|false
     */
    public function findAll(): array | false
    {
        return $this->database->query("SELECT * FROM stacks")->fetchAll();
    }
}