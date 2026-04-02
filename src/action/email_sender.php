<?php
require __DIR__ . '/../../vendor/autoload.php';

header('Content-Type: application/json; charset=utf-8');

if (!empty($_POST['honeypot'])) {
    http_response_code(200);
    echo json_encode(["success" => true, "message" => "Message envoyé (satané bot !)"]);
    exit();
}

$email   = trim($_POST["email"] ?? "");
$name    = trim($_POST["name"] ?? "");
$message = trim($_POST["message"] ?? "");
$subject  = trim($_POST["subject"] ?? "Sans objet");


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "L'email est invalide"]);
    exit();
}

if (empty($name) || empty($message)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Veuillez remplir tous les champs"]);
    exit();
}

$htmlBody = "
<div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #eee; padding: 20px; border-radius: 10px;'>
    <h2 style='color: #0d6efd; border-bottom: 2px solid #0d6efd;'>Nouveau message - Portfolio</h2>
    <p><strong>Expéditeur :</strong> " . htmlspecialchars($name) . " (" . htmlspecialchars($email) . ")</p>
    <p><strong>Objet :</strong> " . htmlspecialchars($subject) . "</p>
    <div style='background-color: #f8f9fa; padding: 15px; border-left: 4px solid #0d6efd; margin-top: 20px;'>
        " . nl2br(htmlspecialchars($message)) . "
    </div>
    <hr style='margin-top: 30px; border: 0; border-top: 1px solid #eee;'>
    <p style='font-size: 0.8em; color: #777;'>Ce mail a été envoyé depuis le formulaire de contact du portfolio</p>
</div>";

$data = [
    "sender" => [
        "name" => "Portfolio Contact",
        "email" => $_ENV["BREVO_EMAIL_VERIFIED"]
    ],
    "to" => [
        ["email" => $_ENV["ADMIN_EMAIL"], "name" => "Moi"]
    ],
    "replyTo" => ["email" => $email, "name" => $name],
    "subject" => "Contact Portfolio : " . $subject,
    "htmlContent" => $htmlBody
];

$ch = curl_init("https://api.brevo.com/v3/smtp/email");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'api-key: ' . $_ENV["BREVO_API_KEY"],
    'Content-Type: application/json',
    'Accept: application/json'
]);

curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode >= 200 && $httpCode < 300) {
    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Message envoyé avec succès !', 'author' => $name]);
} else {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur, votre message n'a pas pu être envoyé."
    ]);
}

exit();