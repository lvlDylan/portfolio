<?php

namespace App\Services;


use Firebase\JWT\Key;

/**
 * @var $config array - Configuration obtenue de bootstrap.php
 */
class Jwt
{
    private static ?string $secretKey = null;

    private static function loadSecretKey(): void
    {
        if (self::$secretKey == null) {
            self::$secretKey = $_ENV["JWT_SECRET"] ?? null;
        }
    }

    public static function generateToken(int $userId, string $type, int $duration): string
    {
        self::loadSecretKey();

        $payload = [
            "iat" => time(),
            "exp" => time() + $duration,
            "uid" => $userId,
            "type" => $type
        ];

        return \Firebase\JWT\JWT::encode($payload, self::$secretKey,"HS256");
    }

    public static function saveRefreshToken(int $userId, int $duration, string $refreshToken): void
    {
        $database = Database::getInstance();
        $sql = "INSERT INTO refresh_tokens (user_id, token_hash, expires_at, created_at, is_revoked) VALUES (:user_id, :token_hash, :expires_at, :created_at, :is_revoked)";
        $stmt = $database->prepare($sql);
        $stmt->execute([
            ":user_id" => $userId,
            ":token_hash" => hash("sha256", $refreshToken),
            ":expires_at" => date('Y-m-d H:i:s', time() + $duration),
            ":created_at" => date('Y-m-d H:i:s', time()),
            ":is_revoked" => 0
        ]);
    }

    public static function revokeRefreshToken(string $refreshToken): void
    {
        $database = Database::getInstance();
        $sql = "UPDATE refresh_tokens SET is_revoked = 1 WHERE token_hash = :refresh_token";
        $stmt = $database->prepare($sql);
        $stmt->execute([
            ":refresh_token" => $refreshToken
        ]);
    }

    public static function decodeAccessToken($token): ?\stdClass
    {
        self::loadSecretKey();
        try {
            return  \Firebase\JWT\JWT::decode($token, new Key(self::$secretKey, 'HS256'));
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function isValidRefreshToken(string $refreshToken): array
    {
        $database = Database::getInstance();
        $sql = "SELECT user_id FROM refresh_tokens WHERE token_hash = :refresh_token AND is_revoked = 0 AND expires_at > :now";
        $stmt = $database->prepare($sql);
        $stmt->execute([
            "refresh_token" => $refreshToken,
            "now" => date('Y-m-d H:i:s', time())
        ]);

        $result = $stmt->fetch();

        if ($result) {
            return [
                "user_id" => $result["user_id"],
                "isValid" => true,
            ];
        } else {
            return [
                "user_id" => -1,
                "isValid" => false,
            ];
        }
    }
}