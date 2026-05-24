<?php

namespace App\Models;

use App\Exceptions\ProjectException;
use App\Models\Entities\ProjectEntity;
use App\Services\Database;
use App\Services\LoggerService;
use App\Services\RedisService;
use Monolog\Logger;
use PDO;
use PDOException;
use Redis;
use RedisException;

/**
 * Class Projects
 * * Gère la logique de récupération des projets du portfolio.
 * Cette classe est responsable de l'agrégation des projets avec leurs stacks techniques
 * associées via une table de liaison.
 * * @package App\Models
 */
readonly class Projects
{
    /**
     * @var PDO Instance de connexion à la base de données.
     */
    private PDO $database;

    /**
     * @var Redis|null Instance de connexion au serveur Redis.
     */
    private ?Redis $cache;

    /**
     * Instance du logger.
     * @var Logger
     */
    private Logger $logger;

    /**
     * Projects constructor.
     * Initialise la connexion via le Singleton Database.
     */
    public function __construct()
    {
        $this->database = Database::getInstance();
        $this->cache = RedisService::getInstance();
        $this->logger = LoggerService::getLogger();
    }

    /**
     * Exécute la requête SQL pour récupérer les projets et leurs technologies.
     * * Utilise GROUP_CONCAT pour agréger les noms, icônes et couleurs des stacks
     * afin d'éviter le problème de duplication de lignes lors des jointures.
     * * @return array<int, array{
     * id: string,
     * title: string,
     * description: string,
     * github_link: string,
     * description_shortened: string,
     * image_full: string,
     * stack_names: ?string,
     * stack_icons: ?string,
     * stack_colors: ?string
     * }> Liste brute issue de la base de données.
     * @throws ProjectException Levée si la récupération des projets échoue.
     */
    private function findAll(): array
    {
        try {
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
            $stmt = $this->database->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw ProjectException::fetchFailed($e);
        }
    }

    /**
     * Formate et retourne la liste des projets prête pour la vue.
     * * Transforme les chaînes de caractères délimitées par des virgules en tableaux PHP
     * et assure le cast des types (ex: id en int).
     * * @return array<int, array{
     * id: int,
     * title: string,
     * description: string,
     * github_link: ?string,
     * description_shortened: string,
     * image_full: ?string,
     * names: string[],
     * icons: string[],
     * colors: string[]
     * }> Liste formatée des projets.
     * @throws ProjectException Levée si la récupération des projets échoue.
     * */
    public function getProjects(): array
    {

        try {
            $cached = $this->cache->get("project-vue:list");
            if ($cached !== false) {
                return json_decode($cached, true);
            }
        } catch (RedisException $e) {
            $this->logger->warning("Échec lors de la tentative de récupération via le cache REDIS " . $e->getMessage());
        }

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


        if (isset($this->cache)) {
            $this->cache->setex("project-vue:list", 2 * 60  * 60, json_encode($projects));
        }

        return $projects;
    }
}