<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Gestion centralisée des exceptions liées aux actions sur les projets.
 * * Cette classe utilise des constructeurs statiques nommés (Named Constructors)
 * pour identifier précisément le contexte de l'erreur SQL ou métier.
 */
class ProjectException extends Exception
{

    /**
     * Leve une exception lorsque la récupération des projets échoue.
     *
     * @param Throwable|null $previous L'exception d'origine (ex: PDOException) pour le traçage.
     */
    public static function fetchFailed(?Throwable $previous = null): ProjectException
    {
        return new self("Les projets n'ont pas pu être récupérer.", 100, $previous);
    }

    /**
     * Leve une exception lorsque l'insertion d'un projet échoue.
     *
     * @param string $title Le titre du projet concerné.
     * @param Throwable|null $previous L'exception d'origine (ex: PDOException) pour le traçage.
     * @return static
     */
    public static function insertFailed(string $title, ?Throwable $previous = null): self
    {
        return new self("Le projet '{$title}' n'a pas pu être inséré.", 101, $previous);
    }

    /**
     * Leve une exception lorsque la mise à jour d'un projet échoue.
     *
     * @param string $title Le titre du projet concerné.
     * @param Throwable|null $previous L'exception d'origine (ex: PDOException) pour le traçage.
     * @return static
     */
    public static function updateFailed(string $title, ?Throwable $previous = null): self
    {
        return new self("Le projet '{$title}' n'a pas pu être modifié.", 102, $previous);
    }

    /**
     * Leve une exception lorsque la suppression d'un projet échoue.
     *
     * @param int $id L'identifiant en base de données du projet concerné.
     * @param Throwable|null $previous L'exception d'origine (ex: PDOException) pour le traçage.
     * @return static
     */
    public static function deleteFailed(int $id, ?Throwable $previous = null): self
    {
        return new self("Le projet n°{$id} n'a pas pu être supprimé.", 103, $previous);
    }
}