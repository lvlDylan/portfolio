<?php

namespace App\Controllers\Api;

use App\Services\Database;
use App\Services\Jwt;
use PDO;

class AuthController
{

    private ?PDO $database;

    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    public function login(): void
    {
        header("Content-Type: application/json");

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data["username"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Nom d'utilisateur manquant"]);
            exit;
        }

        $sql = "SELECT id, username, password_hash FROM `users` WHERE username = :username";
        $stmt = $this->database->prepare($sql);
        $stmt->execute(["username" => $data["username"]]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Identifiants invalides"]);
            exit;
        }

        if (!password_verify($data["password"], $result["password_hash"])) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Identifiants invalides"]);
            exit;
        }

        $accessToken = Jwt::generateToken((int) $result["id"], "access", 15 * 60);
        $refreshToken = Jwt::generateToken((int) $result["id"], "refresh", 7 * 24 * 60 * 60);
        Jwt::saveRefreshToken((int) $result["id"], 7 * 24 * 60 * 60, $refreshToken);
        http_response_code(200);
        echo json_encode(["status" => "success", "access_token" => $accessToken, "refresh_token" => $refreshToken, "expires_in" => 15 * 60]);
        exit;
    }

    public function logout(): void
    {
        header("Content-Type: application/json");

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data["refresh_token"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Token de rafraichissement invalide"]);
            exit;
        }

        Jwt::revokeRefreshToken((int) $data["refresh_token"]);
        http_response_code(204);
    }

    public function refresh(): void
    {
        header("Content-Type: application/json");

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (empty($data["refresh_token"])) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => "Token de rafraichissement manquant"]);
            exit;
        }

        $decoded = Jwt::decodeAccessToken($data["refresh_token"]);

        if (empty($decoded) || $decoded->type !== "refresh") {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Token de rafraichissement invalide, veuillez vous reconnecter"]);
            exit;
        }

        $sql = "SELECT user_id FROM refresh_tokens WHERE token_hash = :refresh_token AND is_revoked = 0 AND expires_at > :now";
        $stmt = $this->database->prepare($sql);
        $stmt->execute([
            "refresh_token" => hash("sha256", $data["refresh_token"]),
            "now" => time()
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (empty($result)) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Session expirée"]);
            exit;
        }

        $newAccessToken = Jwt::generateToken((int) $result["user_id"], "access", 15 * 60);
        echo json_encode([
            "status" => "success",
            "access_token" => $newAccessToken,
            "expires_in" => 15 * 60
        ]);
        exit;
    }

}