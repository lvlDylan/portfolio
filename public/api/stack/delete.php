<?php

require_once __DIR__ . "/../../../src/config.php";
require_once __DIR__ . "/../../../src/database/database.php";
require_once __DIR__ . "/../../../vendor/autoload.php";

use Dylan\Api\Logger;
use Dylan\Api\TokenManager;

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] != "DELETE") {
    http_response_code(405);
    exit(json_encode(["message" => "Méthode non permise."]));
}

$headers = getallheaders();
$authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';

$token = str_replace('Bearer ', '', $authHeader);
$decoded = TokenManager::decodeAccessToken($token);

if (empty($token) || !$decoded || $decoded->uid != 1) {
    Logger::log("error", $_SERVER["REMOTE_ADDR"], "[Unauthorized] Mauvais token.");
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Accès refusé ou token expiré."]);
    exit();
}

$name = $_GET["name"] ?? null;

if (!$name) {
    http_response_code(400);
    exit(json_encode(["message" => "ID manquant."]));
}

try {

    $sql = "SELECT id FROM stacks WHERE name = ?";
    $stmt = isset($pdo) ? $pdo->prepare($sql) : null;
    $stmt->execute([$name]);
    $stack = $stmt->fetch();

    if ($stack) {
        $stmtPivot = $pdo->prepare("DELETE FROM project_stacks WHERE stack_id = ?");
        $stmtPivot->execute([$stack['id']]);

        $stmtDelete = $pdo->prepare("DELETE FROM stacks WHERE id = ?");
        $stmtDelete->execute([$stack['id']]);
    }

    echo json_encode(["status" => "success"]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erreur BDD : " . $e->getMessage()]);
}