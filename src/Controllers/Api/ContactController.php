<?php

namespace App\Controllers\Api;

use App\Exceptions\BotException;
use App\Exceptions\Format\ValidException;
use App\Services\Email;
use App\Services\LoggerService;
use Exception;
use Monolog\Logger;
use PDOException;

/**
 * Class ContactController
 * * Gère les requêtes API liées au formulaire de contact et à l'envoi d'emails via Brevo.
 * * @package App\Controllers\Api
 */
class ContactController
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
     * Point d'entrée pour la soumission du formulaire de contact.
     * * Valide les données POST, vérifie le honeypot, et déclenche l'envoi
     * du mail via l'API Brevo. Retourne une réponse JSON.
     * * @return void
     */
    public function handleContact(): void
    {
        header("Content-Type: application/json; charset=utf-8");

        try {
            // Protection contre les bots
            if (!empty($_POST["honeypot"])) {
                throw new BotException("Message envoyé !");
            }

            // Nettoyage et validation des données
            $email   = filter_var(trim($_POST["email"] ?? ""), FILTER_VALIDATE_EMAIL);
            $name    = htmlspecialchars(trim($_POST["name"] ?? ""));
            $message = htmlspecialchars(trim($_POST["message"] ?? ""));
            $subject = htmlspecialchars(trim($_POST["subject"] ?? "Sans objet"));

            if (!$email || empty($name) || empty($message)) {
                throw new ValidException("Données invalides ou manquantes.");
            }

            $result = Email::sendEmail($name, $email, $subject, $message);

            if ($result["status"] >= 200 && $result["status"] < 300) {
                http_response_code(201);
                echo json_encode(["success" => true, "message" => "Message envoyé !", "author" => $name]);
            } else {
                throw new \Exception($result["message"]);
            }

        } catch (BotException $e) {
            $this->logger->info("Formulaire de contact intercepté (Bot)", [
                "exception" => $e,
            ]);
            http_response_code(200);
            echo json_encode(["status" => "success", "message" => $e->getMessage()]);
        } catch (ValidException $e) {
            $this->logger->info("Données de formulaire de contact invalides", [
                "exception" => $e,
                "user_data" => [
                    "email" => $_POST["email"] ?? "Non fourni",
                    "name"  => $_POST["name"] ?? "Non fourni"
                ]
            ]);
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (PDOException $e) {
            $this->logger->critical("Erreur base de données lors de la soumission du formulaire de contact", [
                "exception" => $e,
                "user_data" => [
                    "email" => $_POST["email"] ?? "Non fourni",
                    "name"  => $_POST["name"] ?? "Non fourni"
                ]
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur technique est survenue."]);
        } catch (Exception $e) {
            $this->logger->error("Erreur technique lors de l'envoi du mail via Brevo", [
                "exception" => $e,
                "user_data" => [
                    "email" => $_POST["email"] ?? "Non fourni",
                    "name"  => $_POST["name"] ?? "Non fourni"
                ]
            ]);
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur technique est survenue."]);
        }
    }
}