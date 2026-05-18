<?php

namespace App\Controllers\Api;

use App\Models\Api\StackModel;
use App\Models\Entities\StackEntity;

/**
 * Class StackController
 * * Gère les points de terminaison de l'API pour les compétences techniques (Stacks).
 * Fournit les données nécessaires à l'affichage dynamique des technos.
 * * @package App\Controllers\Api
 */
class StackController
{

    /**
     * Récupère la liste complète des stacks techniques.
     * * Cette méthode interroge le modèle, définit les en-têtes CORS et JSON,
     * puis retourne les données formatées.
     * * @return void Retourne un flux JSON et interrompt l'exécution.
     */
    public function getStacks(): void {
        $model = new StackModel();
        $stacks = $model->findAll();

        // Définition des en-têtes HTTP
        header("Content-Type: application/json; charset=utf-8");

        echo json_encode([
            "status" => "success",
            "count" => count($stacks),
            "data" => $stacks
        ]);

        exit;
    }

    /**
     * Crée une nouvelle stack technique en base de données.
     *
     * Récupère les données de la requête au format JSON, les valide, construit
     * l'entité correspondante et tente de l'insérer via le modèle.
     *
     * @return void Retourne une réponse JSON (HTTP 201 ou 500) et interrompt l'exécution.
     */
    public function createStack(): void {
        header("Content-Type: application/json; charset=utf-8");
        $stackModel = new StackModel();
        $data = $this->getJson();
        $stack = $this->validateAndBuildEntity($data);

        if ($stackModel->insert($stack)) {
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "La stack " . $stack->getName() . " a été ajoutée en base."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "La stack " . $stack->getName() . " n'a pas été ajoutée en base suite à une erreur serveur."]);
        }

        exit;
    }

    /**
     * Modifie une stack technique existante en base de données.
     *
     * Récupère les données mises à jour en JSON, valide la structure et applique
     * les modifications via le modèle.
     *
     * @return void Retourne une réponse JSON (HTTP 201 ou 500) et interrompt l'exécution.
     */
    public function updateStack(): void {
        header("Content-Type: application/json; charset=utf-8");
        $stackModel = new StackModel();
        $data = $this->getJson();

        if (empty($data["id"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Le champs d'id et de nom sont obligatoires"]);
            exit;
        }

        $stack = $this->validateAndBuildEntity($data, $data["id"]);

        if ($stackModel->update($stack)) {
            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "La stack " . $stack->getName() . " a été modifiée en base."]);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "La stack " . $stack->getName() . " n'a pas été modifiée en base suite à une erreur serveur."]);
        }

        exit;
    }

    /**
     * Supprime une stack technique à partir de son identifiant.
     *
     * Extrait l'identifiant du JSON reçu et demande sa suppression au modèle.
     *
     * @return void Retourne un code HTTP 204 (Succès sans contenu) ou un JSON d'erreur (HTTP 400 ou 500).
     */
    public function deleteStack(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        $stackModel = new StackModel();
        $data = $this->getJson();
        if (empty($data["id"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Le champ d'identifiant est obligatoire."]);
            exit;
        }

        if ($stackModel->delete($data["id"])) {
            http_response_code(204);
        } else {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "La stack n'a pas été supprimée suite à une erreur serveur."]);
        }

        exit;
    }

    /**
     * Valide les données brutes reçues et assemble l'entité StackEntity correspondante.
     * * Cette méthode centralise les règles métiers de validation pour la création et la modification.
     *
     * @param array $data Le tableau associatif contenant les données de la stack.
     * @param int $id L'identifiant de la stack (-1 par défaut s'il s'agit d'une création).
     * @return StackEntity L'instance de la stack validée et configurée.²
     */
    private function validateAndBuildEntity(array $data, int $id = -1): StackEntity
    {
        header("Content-Type: application/json; charset=utf-8");


        if (empty($data["name"]) || empty($data["category"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Les champs de nom et catégories sont obligatoires"]);
            exit;
        }

        if (empty($data["iconName"])) {
            $data["iconName"] = "devicon-" . str_replace(" ", "", strtolower($data["name"])) . "-plain"; # https://devicon.dev/ (Petite manipulation pour obtenir le format de devicon)
        }

        if (empty($data["colorName"])) {
            $data["colorName"] = str_replace(" ", "", strtolower($data["name"]));
        }

        return new StackEntity($id, $data["name"], $data["category"], $data["iconName"], $data["colorName"]);
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