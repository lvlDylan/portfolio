<?php
require_once __DIR__ . "/../../../src/config.php";
require_once __DIR__ . "/../../../src/database/database.php";
require_once __DIR__ . "/../../../vendor/autoload.php";

use Dylan\Api\Logger;

header("Content-Type: application/json");

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    http_response_code(405);
    echo json_encode(["message" => "Méthode non permise."]);
    exit();
}

try {
    $sql = "SELECT p.*, GROUP_CONCAT(ps.stack_id) as stack_ids 
        FROM projects p 
        LEFT JOIN project_stacks ps ON p.id = ps.project_id 
        GROUP BY p.id";

    $stmt = isset($pdo) ? $pdo->prepare($sql) : null;
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($data as &$project) {
        if ($project['stack_ids']) {
            $project['stacks'] = array_map('intval', explode(',', $project['stack_ids']));
        } else {
            $project['stacks'] = [];
        }
        unset($project['stack_ids']);
    }
    unset($project);

    Logger::log("info", $_SERVER["REMOTE_ADDR"], "[SUCCESS] Liste projet envoyée.");
    http_response_code(200);
    echo json_encode([
        "status" => "success",
        "count" => count($data),
        "projets" => $data ?: []
    ]);
} catch (PDOException $e) {
    Logger::log("error", $_SERVER["REMOTE_ADDR"], "[DB ERROR] " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erreur serveur lors de la récupération."]);
}