<?php
require_once __DIR__ . "/../../../src/config.php";
require_once __DIR__ . "/../../../src/database/database.php";
require_once __DIR__ . "/../../../vendor/autoload.php";

use Dylan\Api\Logger;
use Dylan\Api\TokenManager;

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non permise."]);
    exit();
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

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (empty($data["name"])) {
    Logger::log("info", $_SERVER["REMOTE_ADDR"], "[ERREUR] Aucun titre.");
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Un nom de technologie est obligatoire."]);
    exit();
}

if (empty($data["category"])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Une catégorie est obligatoire."]);
    exit();
}


$sql = "INSERT INTO stacks (name, category, icon_name, color_name) VALUES (?, ?, ?, ?)";
$stmt = isset($pdo) ? $pdo->prepare($sql) : null;
if (!isset($stmt)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erreur interne."]);
    exit();
}

$stmt->bindValue(1, $data["name"]);
$stmt->bindValue(2, $data["category"]);
$stmt->bindValue(3, $data["icon_name"]);
$stmt->bindValue(4, $data["color_name"]);
$stmt->execute();

$stackId = $pdo->lastInsertId();

Logger::log("info", $_SERVER["REMOTE_ADDR"], "[SUCCESS] Le stack $stackId a été inséré en base.");
http_response_code(200);
echo json_encode(["status" => "success", "message" => "Projet ajouté !"]);