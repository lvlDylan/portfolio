<?php

namespace App\Models;

use App\Services\Database;
use PDO;

/**
 * Class Skills
 * * Gère la récupération et l'organisation des compétences techniques (stacks).
 * Ce modèle combine les données issues de la base de données avec une configuration
 * statique pour fournir un ensemble complet d'informations pour la vue.
 * * @package App\Models
 */
readonly class Skills
{

    /**
     * @var PDO|null Instance de connexion à la base de données.
     */
    private ?PDO $database;

    /**
     * Skills constructor.
     * * Initialise la connexion à la base de données via le Singleton Database.
     */
    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * Récupère les compétences brutes groupées par catégorie depuis la base de données.
     * * Utilise GROUP_CONCAT pour agréger les stacks afin de faciliter le groupement
     * par catégorie (frontend, backend, etc.) directement via SQL.
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

    /**
     * Fournit les métadonnées statiques (titres, descriptions, icônes de section)
     * pour chaque catégorie de compétence.
     * * @return array<string, array{
     * title: string,
     * desc: string,
     * icon: string
     * }> Configuration par clé de catégorie.
     */
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
     * Formate et fusionne les compétences pour la vue.
     * * Cette méthode fait le lien entre les résultats SQL (stacks dynamiques)
     * et la configuration statique (descriptions textuelles). Elle transforme
     * également les chaînes concaténées en tableaux PHP.
     * * @return array<int, array{
     * title: string,
     * description: string,
     * icon: string,
     * category: string,
     * names: string[],
     * colors: string[],
     * icons: string[]
     * }> Liste structurée des compétences prête pour l'affichage.
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