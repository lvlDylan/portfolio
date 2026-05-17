<?php

namespace App\Models\Api;

use App\Models\Entities\ProjectEntity;
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
    public function findAll(): array|false
    {
        return $this->database->query("SELECT * FROM projects")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Insère un projet en base de données ainsi que ses liaisons avec les stacks associées.
     *
     * Cette méthode utilise une transaction pour s'assurer que si l'insertion du projet
     * ou d'une de ses stacks échoue, rien ne soit enregistré en base de données.
     *
     * @param ProjectEntity $projectEntity L'entité du projet à insérer.
     * @return bool True si le projet et toutes ses stacks ont été insérés avec succès, false sinon.
     */
    public function insert(ProjectEntity $projectEntity): bool
    {
        $sql = "INSERT INTO projects (title, description, description_shortened, image_full, github_link) VALUES (:title, :description, :description_shortened, :image_full, :github_link)";

        $this->database->beginTransaction();

        $stmt = $this->database->prepare($sql);
        $result = $stmt->execute([
            "title" => $projectEntity->getTitle(),
            "description" => $projectEntity->getDescription(),
            "description_shortened" => $projectEntity->getDescriptionShortened(),
            "image_full" => $projectEntity->getImageUri(),
            "github_link" => $projectEntity->getGithubUrl()
        ]);

        if (!$result) {
            $this->database->rollBack();
            return false;
        }

        $id = $this->database->lastInsertId();
        $stmtStack = $this->database->prepare("INSERT INTO project_stacks (project_id, stack_id) VALUES (:project_id, :stack_id)");
        foreach ($projectEntity->getStacks() as $stack) {
            $stackResult = $stmtStack->execute(["project_id" => $id, "stack_id" => $stack->getId()]);
            if (!$stackResult) {
                $this->database->rollBack();
                return false;
            }
        }

        $this->database->commit();
        return true;
    }

    /**
     * Met à jour un projet existant ainsi que ses liaisons avec les stacks techniques.
     * * Cette méthode utilise une transaction SQL pour garantir la cohérence des données.
     * Elle écrase les anciennes valeurs du projet, supprime ses anciennes associations de stacks
     * dans la table pivot, puis insère les nouvelles associations fournies par l'entité.
     *
     * @param ProjectEntity $projectEntity L'entité du projet contenant l'ID et les données modifiées.
     * @return bool True si la mise à jour et la synchronisation des stacks ont réussi, false sinon.
     */
    public function update(ProjectEntity $projectEntity): bool
    {
        $sql = "UPDATE projects SET title = :title, description = :description, description_shortened = :description_shortened, github_link = :github_link, image_full = :image_full WHERE id = :id";

        $this->database->beginTransaction();

        $stmt = $this->database->prepare($sql);
        $result = $stmt->execute([
            "id" => $projectEntity->getId(),
            "title" => $projectEntity->getTitle(),
            "description" => $projectEntity->getDescription(),
            "description_shortened" => $projectEntity->getDescriptionShortened(),
            "image_full" => $projectEntity->getImageUri(),
            "github_link" => $projectEntity->getGithubUrl()
        ]);

        if (!$result) {
            $this->database->rollBack();
            return false;
        }

        $deleteStmt = $this->database->prepare("DELETE FROM project_stacks WHERE project_id=:project_id");
        $deleteStmt->execute(["project_id" => $projectEntity->getId()]);

        $stmtStack = $this->database->prepare("INSERT INTO project_stacks (project_id, stack_id) VALUES (:project_id, :stack_id)");
        foreach ($projectEntity->getStacks() as $stack) {
            $stackResult = $stmtStack->execute(["project_id" => $projectEntity->getId(), "stack_id" => $stack->getId()]);
            if (!$stackResult) {
                $this->database->rollBack();
                return false;
            }
        }

        $this->database->commit();
        return true;
    }
}