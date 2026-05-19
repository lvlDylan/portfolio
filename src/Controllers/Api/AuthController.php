<?php

namespace App\Controllers\Api;

use App\Exceptions\Auth\AuthException;
use App\Exceptions\Format\JSONException;
use App\Exceptions\Format\ValidException;
use App\Services\Database;
use App\Services\Jwt;
use PDO;
use PDOException;

/**
 * Gestionnaire de l'authentification API.
 *
 * Assure la connexion, la déconnexion et le renouvellement des jetons JWT.
 */
class AuthController
{
    /**
     * Instance de connexion à la base de données.
     * @var PDO|null
     */
    private ?PDO $database;

    /**
     * Initialise le contrôleur en récupérant l'instance Singleton de la base de données.
     */
    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * Authentifie un utilisateur et génère une paire de jetons (access & refresh).
     *
     * @Route POST /api/login
     * @return void
     */
    public function login(): void
    {
        header("Content-Type: application/json; charset=utf-8");

        try {
            $data = $this->getJson();

            if (empty($data["username"]) || empty($data["password"])) {
                throw new ValidException("Nom d'utilisateur ou mot de passe manquant.");
            }

            $sql = "SELECT id, username, password_hash FROM `users` WHERE username = :username";
            $stmt = $this->database->prepare($sql);
            $stmt->execute(["username" => $data["username"]]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if (empty($result) || !password_verify($data["password"], $result["password_hash"])) {
                throw new AuthException("Identifiants invalides.");
            }

            // Sécurisation des écritures via une transaction
            $this->database->beginTransaction();

            $accessToken = Jwt::generateToken((int) $result["id"], "access", 15 * 60);
            $refreshToken = Jwt::generateToken((int) $result["id"], "refresh", 7 * 24 * 60 * 60);

            Jwt::saveRefreshToken((int) $result["id"], 7 * 24 * 60 * 60, $refreshToken);

            $this->database->commit();

            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "access_token" => $accessToken,
                "refresh_token" => $refreshToken,
                "expires_in" => 15 * 60
            ]);

        } catch (JSONException|ValidException $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (AuthException $e) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (PDOException $e) {
            // Annulation de la transaction si elle a été ouverte avant le crash
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur technique est survenue."]);
        }
    }

    /**
     * Invalide un jeton de rafraîchissement pour déconnecter l'utilisateur.
     *
     * @Route POST /api/logout
     * @return void
     */
    public function logout(): void
    {
        header("Content-Type: application/json; charset=utf-8");

        try {
            $data = $this->getJson();

            if (empty($data["refresh_token"])) {
                throw new ValidException("Refresh token manquant.");
            }

            $this->database->beginTransaction();
            Jwt::revokeRefreshToken($data["refresh_token"]);
            $this->database->commit();

            http_response_code(204);

        } catch (JSONException|ValidException $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (PDOException $e) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur technique est survenue."]);
        }
    }

    /**
     * Génère un nouveau access_token à partir d'un refresh_token valide.
     *
     * @Route POST /api/refresh
     * @return void
     */
    public function refresh(): void
    {
        header("Content-Type: application/json; charset=utf-8");

        try {
            $data = $this->getJson();

            if (empty($data["refresh_token"])) {
                throw new ValidException("Refresh token manquant.");
            }

            $decoded = Jwt::decodeAccessToken($data["refresh_token"]);

            if (empty($decoded) || $decoded->type !== "refresh") {
                throw new AuthException("Token de rafraîchissement invalide ou expiré.");
            }

            $result = Jwt::isValidRefreshToken($data["refresh_token"]);

            if (empty($result) || $result["isValid"] !== true) {
                throw new AuthException("Session expirée, veuillez vous reconnecter.");
            }

            $newAccessToken = Jwt::generateToken((int) $result["user_id"], "access", 15 * 60);

            http_response_code(200);
            echo json_encode([
                "status" => "success",
                "access_token" => $newAccessToken,
                "expires_in" => 15 * 60
            ]);

        } catch (JSONException|ValidException $e) {
            http_response_code(400);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (AuthException $e) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
        } catch (PDOException $e) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }
            http_response_code(500);
            echo json_encode(["status" => "error", "message" => "Une erreur technique est survenue."]);
        }
    }

    /**
     * Extrait, décode et valide le flux JSON reçu dans le corps de la requête HTTP.
     *
     * @return array<string, mixed> Le tableau associatif représentant les données JSON décodées.
     * @throws JSONException Si le corps de la requête n'est pas un JSON valide.
     */
    private function getJson(): array
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($data)) {
            throw new JSONException("Le format des données JSON est invalide.");
        }

        return $data;
    }
}