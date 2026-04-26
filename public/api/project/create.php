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

if (empty($data["title"])) {
    Logger::log("info", $_SERVER["REMOTE_ADDR"], "[ERREUR] Aucun titre.");
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Un titre est obligatoire."]);
    exit();
}

if (empty($data["description"])) {
    Logger::log("info", $_SERVER["REMOTE_ADDR"], "[ERREUR] Aucune description.");
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Une description est obligatoire."]);
    exit();
}

if (strlen($data["description_shortened"]) > 100) {
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "La description courte ne doit pas excéder 100 caractères."]);
    exit();
}

if (empty($data["stacks"])) {
    Logger::log("info", $_SERVER["REMOTE_ADDR"], "[ERREUR] Aucune stack.");
    http_response_code(400);
    echo json_encode(["status" => "error", "message" => "Au moins une stack est obligatoire."]);
    exit();
}

$sql = "INSERT INTO projects (title, description, description_shortened, github_link) VALUES (?,?,?,?)";
$stmt = isset($pdo) ? $pdo->prepare($sql) : null;

if (!isset($stmt)) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Erreur interne."]);
    exit();
}

$stmt->bindValue(1, $data["title"]);
$stmt->bindValue(2, $data["description"]);
$stmt->bindValue(3, $data["descriptionShortened"] ??  "");
$stmt->bindValue(4, $data["githubLink"] ?? "");
$stmt->execute();

$projectId = $pdo->lastInsertId();

if (is_array($data["stacks"])) {
    $stmtStack = $pdo->prepare("INSERT INTO project_stacks (project_id, stack_id) VALUES (?, ?)");
    foreach ($data["stacks"] as $stackId) {
        $stmtStack->execute([$projectId, $stackId]);
    }
}

if (!empty($data["image_full"])) {
    Logger::log("info", $_SERVER["REMOTE_ADDR"], "[SUCCESS] Données d'images trouvée.");
    $base64Str = $data["image_full"];

    $base64Str = preg_replace('#^data:image/\w+;base64,#i', '', $base64Str);
    $imageData = base64_decode($base64Str);

    $sourceImage = imagecreatefromstring($imageData);

    if ($sourceImage !== false) {
        $uploadDir = __DIR__ . "/../../../public/assets/images/projets/";
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $baseName = "project_" . $projectId;
        $webpFile = "project_" . $projectId . ".webp";
        $pngFile = "project_" . $projectId . ".png";

        $resWebp = imagewebp($sourceImage, $uploadDir . $webpFile, 80);
        $resPng  = imagepng($sourceImage, $uploadDir . $pngFile, 6);

        if ($resWebp && $resPng) {
            imagedestroy($sourceImage);

            $sqlPath = "/" . $webpFile;

            $updateStmt = $pdo->prepare("UPDATE projects SET image_full = ? WHERE id = ?");
            $updateStmt->bindValue(1, $sqlPath);
            $updateStmt->bindValue(2, $projectId);
            $updateStmt->execute();
        }
    }
}

Logger::log("info", $_SERVER["REMOTE_ADDR"], "[SUCCESS] Le project $projectId a été inséré en base.");
http_response_code(200);
echo json_encode(["status" => "success", "message" => "Projet ajouté !"]);