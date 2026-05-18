<?php

namespace App\Controllers\Api;

use App\Services\Email;

/**
 * Class ContactController
 * * Gère les requêtes API liées au formulaire de contact et à l'envoi d'emails via Brevo.
 * * @package App\Controllers\Api
 */
class ContactController
{

    /**
     * Point d'entrée pour la soumission du formulaire de contact.
     * * Valide les données POST, vérifie le honeypot, et déclenche l'envoi
     * du mail via l'API Brevo. Retourne une réponse JSON.
     * * @return void
     */
    public function handleContact()
    {
        header("Content-Type: application/json; charset=utf-8");

        // Protection contre les bots
        if (!empty($_POST["honeypot"])) {
            echo json_encode(["success" => true, "message" => "Message envoyé (bot détecté)."]);
            exit;
        }

        // Nettoyage et validation des données
        $email   = filter_var(trim($_POST["email"] ?? ""), FILTER_VALIDATE_EMAIL);
        $name    = htmlspecialchars(trim($_POST["name"] ?? ""));
        $message = htmlspecialchars(trim($_POST["message"] ?? ""));
        $subject = htmlspecialchars(trim($_POST["subject"] ?? "Sans objet"));

        if (!$email || empty($name) || empty($message)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Données invalides ou manquantes."]);
            exit;
        }

        $result = Email::sendEmail($name, $email, $subject, $message);


        if ($result["status"] >= 200 && $result["status"] < 300) {
            http_response_code(201);
            echo json_encode(["success" => true, "message" => "Message envoyé !", "author" => $name]);
        } else {
            http_response_code(500);
            echo json_encode(["success" => false, "message" => "Erreur lors de l'envoi"]);
        }
        exit;
    }
}