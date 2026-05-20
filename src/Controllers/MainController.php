<?php

namespace App\Controllers;

use App\Exceptions\ProjectException;
use App\Exceptions\SkillsException;
use App\Models\Projects;
use App\Models\Skills;
use App\Services\LoggerService;
use Exception;
use Monolog\Logger;
use PDOException;

/**
 * Class MainController
 * * Contrôleur principal gérant l'affichage de la page d'accueil (Landing Page).
 * Responsable de la récupération des données globales et de l'injection du contenu dans le layout.
 * * @package App\Controllers
 */
class MainController
{

    /**
     * Instance du logger.
     * @var Logger
     */
    private Logger $logger;

    /**
     * Initialise le contrôleur en récupérant l'instance du logger.
     */
    public function __construct()
    {
        $this->logger = LoggerService::getLogger();
    }

    /**
     * Effectue le rendu de la page principale du portfolio.
     * * Cette méthode initialise les modèles de données, récupère les compétences
     * et les projets, puis utilise la mise en tampon de sortie (Output Buffering)
     * pour capturer la vue avant de l'injecter dans le layout global.
     * * @return void
     */
    public function render(): void {
        $title = "Dylan Lavieille";

        $skillsModel = new Skills();
        $projectModel = new Projects();

        try {
            $skills = $skillsModel->getSkills();
            $projects = $projectModel->getProjects();

            ob_start();
            require_once ROOT . "/views/main.php";
            $content = ob_get_clean();
            require_once ROOT . "/views/layout.php";
        } catch (ProjectException|SkillsException $e) {
            $this->logger->critical("Erreur lors du chargement des modèles de la page d'accueil (Main/Projects)", [
                "exception" => $e
            ]);
            http_response_code(500);
            $title = "500 - Server Internal Error";
            ob_start();
            require_once ROOT . "/views/500.html";
            $content = ob_get_clean();
            require_once ROOT . "/views/layout.php";
        } catch (PDOException $e) {
            $this->logger->critical("Erreur base de données lors du rendu de la page principale", [
                "exception" => $e
            ]);
            http_response_code(500);
            $title = "500 - Server Internal Error";
            ob_start();
            require_once ROOT . "/views/500.html";
            $content = ob_get_clean();
            require_once ROOT . "/views/layout.php";
        } catch (Exception $e) {
            $this->logger->error("Erreur système imprévue sur la page d'accueil", [
                "exception" => $e
            ]);
        }

    }
}