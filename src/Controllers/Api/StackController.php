<?php

namespace App\Controllers\Api;

use App\Models\Api\StackModel;

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
    public function getStacks() {
        $model = new StackModel();
        $stacks = $model->findAll();

        // Définition des en-têtes HTTP
        header("Content-Type: application/json; charset=utf-8");
        header("Access-Control-Allow-Origin: *"); // À restreindre en production si nécessaire

        echo json_encode([
            "status" => "success",
            "count" => count($stacks),
            "data" => $stacks
        ]);

        exit;
    }

}