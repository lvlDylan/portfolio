<?php

namespace App\Controllers\Api;

use App\Exceptions\Format\ValidException;
use App\Exceptions\StackException;
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
        header("Content-Type: application/json; charset=utf-8");

        try {
            $stacks = $model->findAll();
            echo json_encode([
                "status" => "success",
                "count" => count($stacks),
                "data" => $stacks
            ]);
        } catch (StackException $e) {
            echo json_encode([
                "status" => "error",
                "count" => 0,
            ]);
        }
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

        try {
            $data = $this->getJson();
            $stack = $this->validateAndBuildEntity($data);
            $stackModel->insert($stack);

            http_response_code(201);
            echo json_encode(["status" => "success", "message" => "La stack {$stack->getName()} a été ajoutée en base."]);
        } catch (ValidException $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (StackException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
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

        try {
            $data = $this->getJson();

            if (empty($data["id"])) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Le champs d'id et de nom sont obligatoires"]);
                exit;
            }

            $stack = $this->validateAndBuildEntity($data, $data["id"]);
            $stackModel->update($stack);

            http_response_code(200);
            echo json_encode(["status" => "success", "message" => "La stack {$stack->getName()} a été modifiée en base."]);
        } catch (ValidException $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (StackException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }
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

        try {
            $data = $this->getJson();
            if (empty($data["id"])) {
                http_response_code(400);
                echo json_encode(["status" => "error", "message" => "Le champs d'id et de nom sont obligatoires"]);
                exit;
            }
            $stackModel->delete($data["id"]);
            http_response_code(204);
        } catch (ValidException $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (StackException $e) {
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        }

    }

    /**
     * Valide les données brutes reçues et assemble l'entité StackEntity correspondante.
     * * Cette méthode centralise les règles métiers de validation pour la création et la modification.
     *
     * @param array $data Le tableau associatif contenant les données de la stack.
     * @param int $id L'identifiant de la stack (-1 par défaut s'il s'agit d'une création).
     * @return StackEntity L'instance de la stack validée et configurée.²
     * @throws ValidException Levée lorsque le format de data est invalide.
     */
    private function validateAndBuildEntity(array $data, int $id = -1): StackEntity
    {
        header("Content-Type: application/json; charset=utf-8");


        if (empty($data["name"]) || empty($data["category"])) {
            throw new ValidException("Les champs de nom et catégories sont obligatoires");
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