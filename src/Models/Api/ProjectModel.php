<?php

namespace App\Models\Api;

use App\Exceptions\ProjectException;
use App\Exceptions\StackException;
use App\Models\Entities\ProjectEntity;
use App\Services\Database;
use Exception;
use PDO;
use PDOException;

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
     * }> Retourne un tableau associatif des projets.
     * @throws ProjectException
     */
    public function findAll(): array
    {
        try {
            return $this->database->query("SELECT * FROM projects")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw ProjectException::fetchFailed($e);
        }
    }

    /**
     * Insère un projet en base de données ainsi que ses liaisons avec les stacks associées.
     *
     * Cette méthode utilise une transaction pour s'assurer que si l'insertion du projet
     * ou d'une de ses stacks échoue, rien ne soit enregistré en base de données.
     *
     * @param ProjectEntity $projectEntity L'entité du projet à insérer.
     * @throws ProjectException Lancée si l'insertion du projet échoue.
     * @throws StackException Lancée si l'insertion des stacks du projet échouent.
     * @throws Exception
     */
    public function insert(ProjectEntity $projectEntity): void
    {

        try {
            $this->database->beginTransaction();

            try {
                $sql = "INSERT INTO projects (title, description, description_shortened, image_full, github_link) VALUES (:title, :description, :description_shortened, :image_full, :github_link)";
                $stmt = $this->database->prepare($sql);
                $stmt->execute([
                    "title" => $projectEntity->getTitle(),
                    "description" => $projectEntity->getDescription(),
                    "description_shortened" => $projectEntity->getDescriptionShortened(),
                    "image_full" => $projectEntity->getImageUri(),
                    "github_link" => $projectEntity->getGithubUrl()
                ]);

                $id = $this->database->lastInsertId();
            } catch (PDOException ) {
                throw ProjectException::insertFailed($projectEntity->getTitle(), $e);
            }

            try {
                $stmtStack = $this->database->prepare("INSERT INTO project_stacks (project_id, stack_id) VALUES (:project_id, :stack_id)");

                foreach ($projectEntity->getStacks() as $stack) {
                    $stmtStack->execute(["project_id" => $id, "stack_id" => $stack->getId()]);
                }
            } catch (PDOException $e) {
                throw StackException::associateFailed($projectEntity->getTitle(), $e);
            }

            $this->database->commit();

        } catch (Exception $e) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }

            throw $e;
        }
    }

    /**
     * Met à jour un projet existant ainsi que ses liaisons avec les stacks techniques.
     *
     * Cette méthode utilise une transaction SQL pour garantir la cohérence des données.
     * Elle écrase les anciennes valeurs du projet, supprime ses anciennes associations de stacks
     * dans la table pivot, puis insère les nouvelles associations fournies par l'entité.
     *
     * @param ProjectEntity $projectEntity L'entité du projet à modifier.
     * @throws ProjectException Lancée si la modification du projet échoue.
     * @throws StackException Lancée si la mise à jour des stacks échoue.
     * @throws Exception Pour toute autre erreur globale.
     */
    public function update(ProjectEntity $projectEntity): void
    {

        try {
            $this->database->beginTransaction();
            try {
                $sql = "UPDATE projects SET title = :title, description = :description, description_shortened = :description_shortened, github_link = :github_link, image_full = :image_full WHERE id = :id";
                $stmt = $this->database->prepare($sql);
                $stmt->execute([
                    "id" => $projectEntity->getId(),
                    "title" => $projectEntity->getTitle(),
                    "description" => $projectEntity->getDescription(),
                    "description_shortened" => $projectEntity->getDescriptionShortened(),
                    "image_full" => $projectEntity->getImageUri(),
                    "github_link" => $projectEntity->getGithubUrl()
                ]);
            } catch (PDOException $e) {
                throw ProjectException::updateFailed($projectEntity->getTitle(), $e);
            }

            try {
                $deleteStmt = $this->database->prepare("DELETE FROM project_stacks WHERE project_id=:project_id");
                $deleteStmt->execute(["project_id" => $projectEntity->getId()]);

                $stmtStack = $this->database->prepare("INSERT INTO project_stacks (project_id, stack_id) VALUES (:project_id, :stack_id)");
                foreach ($projectEntity->getStacks() as $stack) {
                    $stmtStack->execute(["project_id" => $projectEntity->getId(), "stack_id" => $stack->getId()]);
                }
            } catch (PDOException $e) {
                throw StackException::associateFailed($projectEntity->getTitle(), $e);
            }

            $this->database->commit();

        } catch (Exception $e) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Supprime le projet définit par l'id en base.
     * * Cette méthode utilise une transaction SQL pour garantir la cohérence des données.
     * @param int $id L'identifiant en base du projet.
     * @throws ProjectException Lancée si la suppression du projet échoue.
     */
    public function delete(int $id): void
    {
        try {
            $stacksSql = "DELETE FROM project_stacks WHERE project_id=:project_id";
            $projectSql = "DELETE FROM projects WHERE id=:id";

            $this->database->beginTransaction();

            $stackStmt = $this->database->prepare($stacksSql);
            $stackStmt->execute(["project_id" => $id]);

            $projectStmt = $this->database->prepare($projectSql);
            $projectStmt->execute(["id" => $id]);


            $this->database->commit();
        } catch (PDOException $e) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }
            throw ProjectException::deleteFailed($id, $e);
        }
    }
}