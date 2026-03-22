<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

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

if (empty($name)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Veuillez renseigner votre nom"]);
    exit();
}

if (empty($message)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Veuillez renseigner un message"]);
    exit();
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USERNAME'];
    $mail->Password   = $_ENV['SMTP_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = $_ENV['SMTP_PORT'];
    $mail->CharSet    = 'UTF-8';

    $mail->setFrom($_ENV['SMTP_USERNAME'], 'Portfolio Contact');
    $mail->addAddress($_ENV['SMTP_USERNAME']);
    $mail->addReplyTo($email, $name);

    $mail->isHTML();
    $mail->Subject = "Contact Portfolio : " . htmlspecialchars($subject);

    $mail->Body = "
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
    $mail->AltBody = "Nouveau message de $name ($email) : \n\n{$message}";

    $mail->send();

    http_response_code(200);
    echo json_encode(['success' => true, 'message' => 'Message envoyé avec succès !', 'author' => $name]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Le message n'a pas pu être envoyé. Erreur: {$mail->ErrorInfo}",
    ]);
}

exit();