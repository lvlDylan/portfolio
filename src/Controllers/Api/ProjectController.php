<?php

namespace App\Controllers\Api;

use App\Models\Api\ProjectModel;

/**
 * ProjectController
 */
class ProjectController
{
    /**
     * Instancie le modèle des projets, récupère les données et les renvoie en JSON.
     * @return void
     */
    public function getProjects(): void
    {
        $model = new ProjectModel();
        $projects = $model->findAll();

        header('Content-Type: application/json');
        header('Access-Control-Allow-Origin: *');

        echo json_encode([
            "status" => "success",
            "count" => count($projects),
            "data" => $projects
        ]);

        exit;
    }
}