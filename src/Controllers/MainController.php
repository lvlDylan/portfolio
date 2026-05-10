<?php

namespace App\Controllers;

use App\Models\Projects;
use App\Models\Skills;

class MainController
{
    public function render(): void {
        $title = "Dylan Lavieille";

        $skillsModel = new Skills();
        $projectModel = new Projects();

        $skills = $skillsModel->getSkills();
        $projects = $projectModel->getProjects();

        ob_start();
        require_once ROOT . "/views/main.php";
        $content = ob_get_clean();
        require_once ROOT . "/views/layout.php";
    }
}