<?php

namespace App\Middlewares;

use App\Services\Jwt;

class AuthMiddleware
{
    public static function accept(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        $headers = getallheaders();
        if (!array_key_exists("Authorization", $headers) && !array_key_exists("authorization", $headers)) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Autorisation invalide."]);
            exit;
        }

        $authorization = $headers["Authorization"] ?? $headers["authorization"];

        if (!str_starts_with($authorization, "Bearer ")) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Autorisation invalide."]);
            exit;
        }

        $authorization = substr($authorization, 7);
        $decoded = Jwt::decodeAccessToken($authorization);

        if (!$decoded || !isset($decoded->uid)) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Autorisation invalide."]);
            exit;
        }

        $authorized_ids = explode(",", $_ENV["AUTHORIZATION"] ?? "");

        if (!in_array($decoded->uid, $authorized_ids)) {
            http_response_code(403);
            echo json_encode(["status" => "error", "message" => "Accès refusé."]);
            exit;
        }
    }

}