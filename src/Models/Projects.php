<?php

namespace App\Models;

use App\Services\Database;
use PDO;

readonly class Projects
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Récupère les projets avec leurs technologies respectives.
     * * @return array<int, array{
     * id: string,
     * title: string,
     * description: string,
     * github_link: string,
     * description_shortened: string,
     * image_full: string,
     * stack_names: string,
     * stack_icons: string,
     * stack_colors: string
     * }> Liste des catégories avec leurs stacks concaténées.
     */
    private function findAll(): array
    {
        $sql = "SELECT p.id, p.title, p.description, 
                p.github_link, p.description_shortened, p.image_full,
                GROUP_CONCAT(s.name ORDER BY s.id SEPARATOR ',') AS stack_names,
                GROUP_CONCAT(s.icon_name ORDER BY s.id SEPARATOR ',') AS stack_icons,
                GROUP_CONCAT(s.color_name ORDER BY s.id SEPARATOR ',') AS stack_colors
                FROM projects p
                LEFT JOIN project_stacks ps ON p.id = ps.project_id
                LEFT JOIN stacks s ON ps.stack_id = s.id
                GROUP BY p.id
                ORDER BY p.id DESC;";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * @return array<int, array{
     * id: int,
     * title: string,
     * description: string,
     * github_link: ?string,
     * description_shortened: string,
     * image_full: ?string,
     * names: string[],
     * icons: string[],
     * colors: string[]
     * }>
     */
    public function getProjects(): array
    {
        $projects = [];
        $rawProjects = $this->findAll();

        foreach ($rawProjects as $row) {
            $projects[] = [
                "id" => (int) $row["id"],
                "title" => $row["title"],
                "image_full" => $row["image_full"],
                "description" => $row["description"],
                "github_link" => $row["github_link"],
                "description_shortened" => $row["description_shortened"],
                "names" => $row["stack_names"] ? explode(",", $row["stack_names"]) : [],
                "icons" => $row["stack_icons"] ? explode(",", $row["stack_icons"]) : [],
                "colors" => $row["stack_colors"] ? explode(",", $row["stack_colors"]) : []
            ];
        }

        return $projects;
    }
}