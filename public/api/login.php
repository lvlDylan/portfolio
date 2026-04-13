<?php
require_once __DIR__ . "/../../src/config.php";
require_once __DIR__ . "/../../src/database/database.php";
require_once __DIR__ . "/../../vendor/autoload.php";

use Dylan\Api\TokenManager;

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non permise."]);
    exit();
}

$json = file_get_contents('php://input');
$data = json_decode($json, true);

$credential = $data["credential"] ?? null;
$password = $data["password"] ?? null;

if (!$credential || !$password) {
    http_response_code(400);
    echo json_encode(["message" => "Identifiant ou mot de passe requis."]);
    exit;
}

$sql = "SELECT id, username, password_hash FROM users WHERE username = :username";

$stmt = isset($pdo) ? $pdo->prepare($sql) : null;
$stmt->bindParam(":username", $credential);
$stmt->execute();

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user && password_verify($password, $user['password_hash'])) {
    $token = TokenManager::getToken($user["id"]);
    $refreshToken = TokenManager::getRefreshToken();

    TokenManager::setRefreshToken($pdo, $refreshToken, $user["id"]);

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "access_token" => $token,
        "refresh_token" => $refreshToken,
        "user" => [
            "id" => $user["id"],
            "username" => $user["username"],
        ]
    ], JSON_PRETTY_PRINT);
} else {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Identifiants invalides."]);
}