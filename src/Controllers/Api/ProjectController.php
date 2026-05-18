<?php

namespace App\Controllers\Api;

use App\Models\Api\ProjectModel;
use App\Models\Api\StackModel;
use App\Models\Entities\ProjectEntity;
use App\Models\Entities\StackEntity;

/**
 * Class ProjectController
 * * Gère les points de terminaison de l'API relatifs aux projets du portfolio.
 * Permet la récupération des données de réalisation pour un affichage dynamique.
 * * @package App\Controllers\Api
 */
class ProjectController
{
    /**
     * Récupère la liste de tous les projets.
     * * Initialise le modèle ProjectModel, extrait l'ensemble des données
     * et les retourne sous forme de flux JSON structuré.
     * * @return void Interrompt l'exécution après l'envoi de la réponse JSON.
     */
    public function getProjects(): void
    {
        $model = new ProjectModel();
        $projects = $model->findAll();

        // Définition des en-têtes HTTP
        header("Content-Type: application/json; charset=utf-8");

        echo json_encode([
            "status" => "success",
            "count" => count($projects),
            "data" => $projects
        ]);

        exit;
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
        $data = $this->getJson();
        $project = $this->validateAndBuildEntity($data);

        if ($projectModel->insert($project)) {
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Le projet {$project->getTitle()} a été ajouté en base."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Le projet {$project->getTitle()} n'a pas été ajouté en base suite à une erreur serveur."]);
            http_response_code(500);
        }

        exit;
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
        $data = $this->getJson();

        if (empty($data["id"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Les champs d'id, titre, description et description raccourcie sont obligatoires"]);
            exit;
        }

        $project = $this->validateAndBuildEntity($data, $data["id"]);

        if ($projectModel->update($project)) {
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "Le projet {$project->getTitle()} a été modifié en base."]);
        } else {
            echo json_encode(["status" => "error", "message" => "Le projet {$project->getTitle()} n'a été modifié en base suite à une erreur serveur."]);
            http_response_code(500);
        }

        exit;
    }

    /**
     * Traite la requête de suppression d'un projet existant.
     * * Cette méthode s'assure de la présence de l'identifiant unique du projet.
     * @return void Interrompt l'exécution après l'envoi de la réponse JSON (204, 400 ou 500).
     */
    public function deleteProject(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        $projectModel = new ProjectModel();
        $data = $this->getJson();
        if (empty($data["id"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Le champ d'identifiant est obligatoire."]);
            exit;
        }

        if ($projectModel->delete($data["id"])) {
            http_response_code(204);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Le projet n'a pas été supprimé suite à une erreur serveur."]);
        }

        exit;
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
     */
    private function validateAndBuildEntity(array $data, int $id = -1): ProjectEntity
    {
        header("Content-Type: application/json; charset=utf-8");


        if (empty($data["title"]) || empty($data["description"]) || empty($data["description_shortened"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Les champs de titre, description et description raccourcie sont obligatoires"]);
            exit;
        }

        if (empty($data["stacks"]) || !is_array($data["stacks"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Les stacks d'un projet doivent être incluses."]);
            exit;
        }

        $stackModel = new StackModel();
        $stacks = [];

        foreach ($data["stacks"] as $name) {
            $stack = $stackModel->findByName($name);
            if ($stack) {
                $stacks[] = new StackEntity($stack["id"], $stack["name"], $stack["category"], $stack["icon_name"], $stack["color_name"]);
            } else {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "La stack {$name} n'existe pas."]);
                exit;
            }
        }


        return new ProjectEntity($id, $data["title"], $data["description"], $stacks, $data["description_shortened"], $data["image_uri"], $data["github_url"]);
    }

    /**
     * Extrait, décode et valide le flux JSON reçu dans le corps de la requête HTTP.
     *
     * @return array<string, mixed> Le tableau associatif représentant les données JSON décodées.
     */
    private function getJson(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        if (!$data) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "JSON invalide."]);
            exit;
        }
        return $data;
    }
}