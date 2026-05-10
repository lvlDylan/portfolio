<?php

namespace App\Models;

use App\Services\Database;
use PDO;

readonly class Skills
{

    private ?PDO $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * Récupère les compétences groupées par catégorie.
     * * @return array<int, array{
     * category: string,
     * stack_names: string,
     * stack_colors: string,
     * stack_icons: string
     * }> Liste des catégories avec leurs stacks concaténées.
     */
    private function findAll(): array
    {
        $sql = "SELECT 
                category, 
                GROUP_CONCAT(name ORDER BY id SEPARATOR ',') AS stack_names,
                GROUP_CONCAT(color_name ORDER BY id SEPARATOR ',') AS stack_colors,
                GROUP_CONCAT(icon_name ORDER BY id SEPARATOR ',') AS stack_icons
                FROM stacks 
                GROUP BY category 
                ORDER BY FIELD(category, 'frontend', 'backend', 'software-sys');";
        $stmt = $this->database->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    private function getConfig(): array
    {
        return [
            'frontend' => [
                'title' => 'Frontend',
                'desc' => 'Interfaces web réactives et typage statique.',
                'icon' => 'bi-window'
            ],
            'backend'  => [
                'title' => 'Backend',
                'desc' => 'Logique serveur, API et bases de données SQL.',
                'icon' => 'bi-database-gear'
            ],
            'software-sys' => [
                'title' => 'Logiciel & Système',
                'desc' => 'Développement applicatif, bas niveau et automatisation.',
                'icon' => 'bi-cpu'
            ]
        ];
    }

    /**
     * Formate les compétences pour la vue en fusionnant SQL et Configuration.
     * * @return array<int, array{
     * title: string,
     * description: string,
     * icon: string,
     * category: string,
     * names: string[],
     * colors: string[],
     * icons: string[]
     * }>
     */
    public function getSkills(): array
    {
        $skills = [];

        $rawSkills = $this->findAll();
        $config = $this->getConfig();

        foreach ($rawSkills as $row) {
            $cat = $row['category'];
            if (isset($config[$cat])) {
                $skills[] = [
                    'title'       => $config[$cat]["title"],
                    'description' => $config[$cat]["desc"],
                    'icon'        => $config[$cat]["icon"],
                    'category'    => $cat,
                    'names'       => explode(",", $row["stack_names"]),
                    'colors'      => explode(",", $row["stack_colors"]),
                    'icons'       => explode(",", $row["stack_icons"])
                ];
            }
        }

        return $skills;
    }

}