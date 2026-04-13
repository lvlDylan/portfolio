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
$refreshToken = $data["refresh_token"] ?? null;

if (!$refreshToken) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Le token de rafraichissement est manquant."]);
    exit();
}

$hashedToken = hash('sha256', $refreshToken);

$sql = "SELECT user_id FROM refresh_tokens 
        WHERE token_hash = :hash 
        AND is_revoked = 0 
        AND expires_at > NOW() 
        LIMIT 1";

$stmt = isset($pdo) ? $pdo->prepare($sql) : null;

if (!isset($stmt)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erreur interne."]);
    exit();
}


$stmt->bindParam(":hash", $hashedToken);
$stmt->execute();

$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $newAccessToken = TokenManager::getToken($row['user_id']);
    $newRefreshToken = TokenManager::getRefreshToken();

    TokenManager::setRefreshToken($pdo, $newRefreshToken, $row["user_id"]);

    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "access_token" => $newAccessToken,
        "new_refresh_token" => $newRefreshToken
    ]);
} else {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Session expirée, veuillez vous reconnecter."]);
}

exit();