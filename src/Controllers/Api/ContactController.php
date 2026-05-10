<?php

namespace App\Controllers\Api;

class ContactController
{

    public function handleContact()
    {
        if ($_SERVER["REQUEST_METHOD"] != "POST") {
            http_response_code(405);
            header('HTTP/1.1 405 Method Not Allowed');
        }

        header('Content-Type: application/json; charset=utf-8');
        if (!empty($_POST['honeypot'])) {
            echo json_encode(["success" => true, "message" => "Message envoyé (bot détecté)."]);
            exit();
        }

        $email   = filter_var(trim($_POST["email"] ?? ""), FILTER_VALIDATE_EMAIL);
        $name    = htmlspecialchars(trim($_POST["name"] ?? ""));
        $message = htmlspecialchars(trim($_POST["message"] ?? ""));
        $subject = htmlspecialchars(trim($_POST["subject"] ?? "Sans objet"));

        if (!$email || empty($name) || empty($message)) {
            http_response_code(400);
            echo json_encode(["success" => false, "message" => "Données invalides ou manquantes."]);
            exit();
        }

        $htmlBody = $this->getEmailTemplate($name, $email, $subject, $message);
        $result = $this->callBrevoApi($name, $email, $subject, $htmlBody);

        if ($result['status'] >= 200 && $result['status'] < 300) {
            http_response_code(201);
            echo json_encode(['success' => true, 'message' => 'Message envoyé !', 'author' => $name]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => "Erreur lors de l'envoi"]);
        }
        exit();
    }

    private function getEmailTemplate($name, $email, $subject, $message): string
    {
        return "
        <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius: 10px;'>
            <h2 style='color: #4338ca; border-bottom: 2px solid #4338ca;'>Nouveau message - Portfolio</h2>
            <p><strong>Expéditeur :</strong> {$name} ({$email})</p>
            <p><strong>Objet :</strong> {$subject}</p>
            <div style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #4338ca; margin-top: 20px;'>
                " . nl2br($message) . "
            </div>
        </div>
        ";
    }

    private function callBrevoApi($name, $email, $subject, $htmlBody): array
    {
        $data = [
            "sender" => ["name" => "Portfolio Contact", "email" => $_ENV["BREVO_EMAIL_VERIFIED"]],
            "to" => [["email" => $_ENV["ADMIN_EMAIL"], "name" => "Moi"]],
            "replyTo" => ["email" => $email, "name" => $name],
            "subject" => "Contact Portfolio : " . $subject,
            "htmlContent" => $htmlBody
        ];

        $ch = curl_init("https://api.brevo.com/v3/smtp/email");
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'api-key: ' . $_ENV["BREVO_API_KEY"],
                'Content-Type: application/json',
                'Accept: application/json'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return ['status' => $status, 'data' => $response];
    }

}