<?php

namespace App\Services;

/**
 * Class Email
 *
 * Gère l'emballage et l'envoi de courriels via l'API transactionnelle de Brevo.
 * Cette classe formate les messages reçus (par exemple depuis un formulaire de contact)
 * dans un template HTML propre avant de les expédier.
 *
 * @package App\Services
 */
class Email
{
    /**
     * Point d'entrée principal pour envoyer un e-mail de contact.
     *
     * Combine la génération du template HTML et l'appel vers l'API de Brevo.
     *
     * @param string $from Nom de la personne qui soumet le formulaire.
     * @param string $fromEmail Adresse e-mail de la personne (pour la réponse).
     * @param string $subject Sujet initial du message.
     * @param string $message Corps textuel du message.
     *
     * @return array{status: int, data: string|bool} Statut HTTP de la requête et réponse brute de l'API.
     */
    public static function sendEmail(string $from, string $fromEmail, string $subject, string $message): array
    {
        $htmlBody = Email::getEmailTemplate($from, $fromEmail, $subject, $message);
        return Email::callBrevoApi($from, $fromEmail, $subject, $htmlBody);
    }

    /**
     * Génère le corps de l'e-mail au format HTML structuré et stylisé.
     *
     * Applique un design de type "carte" épuré et convertit les retours à la ligne
     * textuels (\n) en balises HTML (<br />) via la fonction `nl2br`.
     *
     * @param string $from Nom de l'expéditeur.
     * @param string $fromEmail Adresse e-mail de l'expéditeur.
     * @param string $subject Objet du message.
     * @param string $message Contenu textuel brut.
     *
     * @return string Le code HTML complet prêt à être intégré dans le corps de l'e-mail.
     */
    private static function getEmailTemplate(string $from, string $fromEmail, string $subject, string $message): string
    {
        return "
        <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius: 10px;'>
            <h2 style='color: #4338ca; border-bottom: 2px solid #4338ca;'>Nouveau message - Portfolio</h2>
            <p><strong>Expéditeur :</strong> {$from} ({$fromEmail})</p>
            <p><strong>Objet :</strong> {$subject}</p>
            <div style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #4338ca; margin-top: 20px;'>
                " . nl2br($message) . "
            </div>
        </div>
        ";
    }

    /**
     * Effectue l'appel cURL vers l'API SMTP v3 de Brevo.
     *
     * Utilise les variables d'environnement pour s'authentifier (`BREVO_API_KEY`),
     * définir l'expéditeur de confiance (`BREVO_EMAIL_VERIFIED`), et l'adresse de
     * réception de l'administrateur (`ADMIN_EMAIL`).
     *
     * @param string $from Nom de l'expéditeur (utilisé pour le Reply-To).
     * @param string $fromEmail Email de l'expéditeur (utilisé pour le Reply-To).
     * @param string $subject Objet du mail.
     * @param string $htmlBody Contenu HTML final du mail.
     *
     * @return array{status: int, data: string|bool} Le code HTTP de réponse (ex: 201) et le JSON brut retourné.
     */
    private static function callBrevoApi(string $from, string $fromEmail, string $subject, string $htmlBody): array
    {
        $data = [
            "sender" => ["name" => "Portfolio Contact", "email" => $_ENV["BREVO_EMAIL_VERIFIED"]],
            "to" => ["email" => $_ENV["ADMIN_EMAIL"], "name" => "Moi"],
            "replyTo" => ["email" => $fromEmail, "name" => $from],
            "subject" => "Contact Portfolio : " . $subject,
            "htmlContent" => $htmlBody
        ];

        $ch = curl_init("https://api.brevo.com/v3/smtp/email");
        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                "api-key:" . $_ENV["BREVO_API_KEY"],
                "Content-Type: application/json",
                "Accept: application/json"
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);

        $response = curl_exec($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        return ["status" => $status, "data" => $response];
    }
}