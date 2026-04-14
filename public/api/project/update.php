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
    Logger::log("error", $_SERVER["REMOTE_ADDR"], "[Unauthorized] Tentative d'update sans privilèges.");
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Accès refusé."]);
    exit();
}

$raw = file_get_contents('php://input');
$data = json_decode($raw, true);

if (empty($data["id"]) || empty($data["title"])) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "ID et Titre sont obligatoires."]);
    exit();
}

try {
    $sql = "UPDATE projects 
            SET title = ?, 
                description = ?, 
                description_shortened = ?, 
                github_link = ? 
            WHERE id = ?";

    $stmt = isset($pdo) ? $pdo->prepare($sql) :null;
    $stmt->execute([
        $data["title"],
        $data["description"] ?? "",
        $data["description_shortened"] ?? "",
        $data["github_link"] ?? "",
        $data["id"]
    ]);

    $stmtDel = $pdo->prepare("DELETE FROM project_stacks WHERE project_id = ?");
    $stmtDel->execute([$data["id"]]);

    if (!empty($data["stacks"]) && is_array($data["stacks"])) {
        $stmtIns = $pdo->prepare("INSERT INTO project_stacks (project_id, stack_id) VALUES (?, ?)");
        foreach ($data["stacks"] as $stackId) {
            $stmtIns->execute([$data["id"], $stackId]);
        }
    }

    Logger::log("info", $_SERVER["REMOTE_ADDR"], "[SUCCESS] Projet ID " . $data["id"] . " mis à jour.");
    echo json_encode(["status" => "success", "message" => "Projet mis à jour avec succès !"]);

} catch (Exception $e) {

    Logger::log("error", $_SERVER["REMOTE_ADDR"], "[SQL ERROR] Update failed: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erreur lors de la mise à jour."]);
}