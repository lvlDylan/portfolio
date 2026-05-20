<?php

namespace App\Controllers\Api;

use App\Exceptions\Format\ValidException;
use App\Exceptions\ProjectException;
use App\Exceptions\StackException;
use App\Models\Api\ProjectModel;
use App\Models\Api\StackModel;
use App\Models\Entities\ProjectEntity;
use App\Models\Entities\StackEntity;
use App\Services\LoggerService;
use Exception;
use Monolog\Logger;
use PDOException;

/**
 * Class ProjectController
 * * Gère les points de terminaison de l'API relatifs aux projets du portfolio.
 * Permet la récupération des données de réalisation pour un affichage dynamique.
 * * @package App\Controllers\Api
 */
class ProjectController
{

    /**
     * Instance du logger.
     * @var Logger
     */
    private Logger $logger;


    /**
     * Initialise le contrôleur en récupérant l'instance du logger.
     */
    public function __construct() {
        $this->logger = LoggerService::getLogger();
    }

    /**
     * Récupère la liste de tous les projets.
     * * Initialise le modèle ProjectModel, extrait l'ensemble des données
     * et les retourne sous forme de flux JSON structuré.
     * * @return void Interrompt l'exécution après l'envoi de la réponse JSON.
     */
    public function getProjects(): void
    {
        $model = new ProjectModel();
        header("Content-Type: application/json; charset=utf-8");

        try {
            $projects = $model->findAll();
            echo json_encode([
                "status" => "success",
                "count" => count($projects),
                "data" => $projects
            ]);
        } catch (ProjectException $e) {
            $this->logger->error("Erreur de récupération des projets", [
                "exception" => $e
            ]);
            echo json_encode([
                "status" => "error",
                "count" => 0,
            ]);
        } catch (Exception $e) {
            $this->logger->error("Erreur système imprévue lors de la récupération des projets", [
                "exception" => $e
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur technique est survenue."]);
        }
    }

    /**
     * Traite la requête de création d'un nouveau projet.
     * * Cette méthode récupère les données JSON entrantes, valide l'intégrité
     * et la présence des champs requis, construit l'entité et délègue
     * l'insertion persistante au modèle.
     *
     * @return void Interrompt l'exécution après l'envoi de la réponse JSON (201 ou 500).
     */
    public function createProject(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        $projectModel = new ProjectModel();

        try {
            $data = $this->getJson();
            $project = $this->validateAndBuildEntity($data);

            $projectModel->insert($project);
            http_response_code(201);
            echo json_encode(["status" => "success", "data" => $project]);
        } catch (ValidException $e) {
            $this->logger->info("Données invalides lors de la création d'un projet", [
                "exception" => $e,
                "payload"   => $data ?? null
            ]);
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (ProjectException|StackException $e) {
            $this->logger->error("Échec de l'insertion du projet en base de données", [
                "exception" => $e,
                "payload"   => $data ?? null
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (PDOException $e) {
            $this->logger->critical("Erreur base de données lors de la création d'un projet", [
                "exception" => $e,
                "payload"   => $data ?? null
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur technique est survenue."]);
        } catch (Exception $e) {
            $this->logger->error("Erreur système imprévue lors de la création d'un projet", [
                "exception" => $e
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur interne est survenue."]);
        }

    }

    /**
     * Traite la requête de modification d'un projet existant.
     * * Cette méthode s'assure de la présence de l'identifiant unique du projet,
     * valide les nouvelles informations transmises et met à jour l'enregistrement
     * ainsi que ses dépendances (stacks).
     *
     * @return void Interrompt l'exécution après l'envoi de la réponse JSON (200, 400 ou 500).
     */
    public function updateProject(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        $projectModel = new ProjectModel();

        try {
            $data = $this->getJson();

            if (empty($data["id"])) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Les champs d'id, titre, description et description raccourcie sont obligatoires"]);
                exit;
            }

            $project = $this->validateAndBuildEntity($data, $data["id"]);
            $projectModel->update($project);
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "Le projet {$project->getTitle()} a été modifié en base."]);


        } catch (ValidException $e) {
            $this->logger->info("Données invalides lors de la mise à jour du projet", [
                "exception" => $e,
                "payload"    => $data ?? null
            ]);
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (ProjectException|StackException $e) {
            $this->logger->error("Échec de la mise à jour du projet en base de données", [
                "exception" => $e,
                "payload"    => $data ?? null
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (PDOException $e) {
            $this->logger->critical("Erreur base de données lors de la mise à jour d'un projet", [
                "exception" => $e,
                "payload"   => $data ?? null
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur technique est survenue."]);
        } catch (Exception $e) {
            $this->logger->error("Erreur système imprévue lors de la modification d'un projet", [
                "exception" => $e,
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur interne est survenue."]);
        }

    }

    /**
     * Traite la requête de suppression d'un projet existant.
     * * Cette méthode s'assure de la présence de Brevol'identifiant unique du projet.
     * @return void Interrompt l'exécution après l'envoi de la réponse JSON (204, 400 ou 500).
     */
    public function deleteProject(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        $projectModel = new ProjectModel();

        try {
            $data = $this->getJson();

            if (empty($data["id"])) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Le champ d'identifiant est obligatoire."]);
                exit;
            }
            $projectModel->delete($data["id"]);
            http_response_code(204);

        } catch (ValidException $e) {
            $this->logger->error("Échec de la suppression du projet", [
                "exception" => $e,
                "payloads" => $data ?? null
            ]);
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (ProjectException $e) {
            $this->logger->error("Erreur système imprévue lors de la suppression d'un projet", [
                "exception" => $e,
                "payloads" => $data ?? null
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Le projet n'a pas été supprimé suite à une erreur serveur."]);
        } catch (PDOException $e) {
            $this->logger->critical("Erreur base de données lors de la suppression d'un projet", [
                "exception" => $e,
                "payload"   => $data ?? null
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur technique est survenue."]);
        } catch (Exception $e) {
            $this->logger->error("Erreur base de données lors de la création d'un projet", [
                "exception" => $e,
                "payload"   => $data ?? null
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur technique est survenue."]);
        }
    }

    /**
     * Valide les données brutes reçues et assemble l'entité ProjectEntity correspondante.
     * * Cette méthode centralise les règles métiers de validation pour la création et la modification.
     * Elle vérifie l'existence de chaque stack demandée en interrogeant la base de données
     * avant de lier les entités StackEntity au projet.
     *
     * @param array $data Le tableau associatif contenant les données du projet.
     * @param int $id L'identifiant du projet (-1 par défaut s'il s'agit d'une création).
     * @return ProjectEntity L'instance de l'entité projet validée et configurée.
     * @throws ValidException Levée lorsque le format de data est invalide.
     */
    private function validateAndBuildEntity(array $data, int $id = -1): ProjectEntity
    {
        header("Content-Type: application/json; charset=utf-8");


        if (empty($data["title"]) || empty($data["description"]) || empty($data["description_shortened"])) {
            throw new ValidException("Les champs de titre, description et description raccourcie sont obligatoires");
        }

        if (empty($data["stacks"]) || !is_array($data["stacks"])) {
            throw new ValidException("Les stacks d'un projet doivent être incluses.");
        }

        $stackModel = new StackModel();
        $stacks = [];

        foreach ($data["stacks"] as $name) {
            $stack = $stackModel->findByName($name);
            if ($stack) {
                $stacks[] = new StackEntity($stack["id"], $stack["name"], $stack["category"], $stack["icon_name"], $stack["color_name"]);
            } else {
                throw new ValidException("La stack {$name} n'existe pas.");
            }
        }


        return new ProjectEntity($id, $data["title"], $data["description"], $stacks, $data["description_shortened"], $data["image_uri"] ?? null, $data["github_url"] ?? null);
    }

    /**
     * Extrait, décode et valide le flux JSON reçu dans le corps de la requête HTTP.
     *
     * @return array<string, mixed> Le tableau associatif représentant les données JSON décodées.
     * @throws ValidException Levée lorsque le JSON est invalide.
     */
    private function getJson(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if (!$data) {
            throw new ValidException("JSON invalide.");
        }
        return $data;
    }
}