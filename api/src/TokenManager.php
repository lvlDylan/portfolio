<?php

namespace Dylan\Api;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Random\RandomException;

require_once __DIR__ . "/../../src/config.php";
require_once __DIR__ . "/../../src/database/database.php";
require_once __DIR__ . "/../../vendor/autoload.php";

class TokenManager
{

    private static string $key = API_SECRET_KEY;

    public static function getToken($userId): string
    {
        $payload = [
            "iss" => "https://dylanlv.dev",
            "iat" => time(),
            "exp" => time() + 3600,
            "uid" => $userId
        ];

        return JWT::encode($payload, self::$key, "HS256");
    }

    public static function getRefreshToken()
    {
        try {
            return bin2hex(random_bytes(32));
        } catch (RandomException $e) {
            return null;
        }
    }

    public static function decodeAccessToken($token)
    {
        try {
            return JWT::decode($token, new Key(self::$key, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function setRefreshToken($pdo, $refreshToken, $userId)
    {

        $deleteSql = "DELETE FROM refresh_tokens WHERE user_id = :user_id";
        $deleteStmt = $pdo->prepare($deleteSql);
        $deleteStmt->execute([':user_id' => $userId]);

        $hashedRefreshToken = hash("sha256", $refreshToken);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 days'));

        $stmt = $pdo->prepare("INSERT INTO refresh_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)");
        $stmt->bindParam(1, $userId);
        $stmt->bindParam(2, $hashedRefreshToken);
        $stmt->bindParam(3, $expiresAt);
        $stmt->execute();
    }

}