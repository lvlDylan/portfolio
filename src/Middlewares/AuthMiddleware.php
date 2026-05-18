<?php

namespace App\Middlewares;

use App\Services\Jwt;

/**
 * Class AuthMiddleware
 *
 * Gère la sécurité de l'application en interceptant les requêtes HTTP
 * pour vérifier la validité des jetons d'accès JWT et les autorisations des utilisateurs.
 *
 * @package App\Middlewares
 */
class AuthMiddleware
{
    /**
     * Valide le jeton JWT et vérifie les autorisations d'accès de l'utilisateur.
     *
     * Cette méthode extrait le jeton "Bearer" depuis les en-têtes de la requête,
     * le décode, et s'assure que l'ID de l'utilisateur (`uid`) est présent dans la liste
     * des identifiants autorisés définis dans les variables d'environnement.
     *
     * En cas d'échec (absence de jeton, jeton invalide ou utilisateur non autorisé),
     * la méthode configure le code de réponse HTTP approprié, renvoie un message
     * d'erreur au format JSON, et interrompt immédiatement l'exécution du script via `exit`.
     *
     * @return void
     * @response 401 JSON Retourné si l'en-tête Authorization est manquant, mal formaté,
     *                     ou si le jeton JWT est invalide/expiré.
     * @response 403 JSON Retourné si l'utilisateur est authentifié mais que son `uid`
     *                     n'est pas présent dans la variable d'environnement `$_ENV["AUTHORIZATION"]`.
     */
    public static function accept(): void
    {
        header("Content-Type: application/json; charset=utf-8");

        $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;

        if (empty($authorization)) {
            http_response_code(401);
            echo json_encode(["status" => "error", "message" => "Autorisation invalide."]);
            exit;
        }

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