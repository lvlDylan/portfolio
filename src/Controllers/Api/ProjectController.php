<?php

namespace App\Controllers\Api;

use App\Models\Api\ProjectModel;

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
        header("Access-Control-Allow-Origin: *");

        echo json_encode([
            "status" => "success",
            "count" => count($projects),
            "data" => $projects
        ]);

        exit;
    }
}