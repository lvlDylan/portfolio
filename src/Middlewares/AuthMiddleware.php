<?php

namespace App\Middlewares;

use App\Exceptions\Auth\AuthException;
use App\Exceptions\Auth\ForbiddenException;
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
     * @throws AuthException Si le token est manquant, mal formatté, ou expiré.
     * @throws ForbiddenException Si l'utilisateur n'a pas les droits requis.
     */
    public static function accept(): void
    {
        header("Content-Type: application/json; charset=utf-8");

        $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? null;

        if (empty($authorization)) {
            throw new AuthException("Autorisation invalide.");
        }

        if (!str_starts_with($authorization, "Bearer ")) {
            throw new AuthException("Autorisation invalide.");
        }

        $authorization = substr($authorization, 7);
        $decoded = Jwt::decodeAccessToken($authorization);

        if (!$decoded || !isset($decoded->uid)) {
            throw new AuthException("Autorisation invalide ou expirée.");
        }

        $authorized_ids = explode(",", $_ENV["AUTHORIZATION"] ?? "");

        if (!in_array($decoded->uid, $authorized_ids)) {
           throw new ForbiddenException("Accès refusé.");
        }

        $_REQUEST["USER_ID"] = $decoded->uid;
    }

}