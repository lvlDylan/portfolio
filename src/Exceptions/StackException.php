<?php

namespace App\Exceptions;

use App\Models\Entities\StackEntity;
use Exception;
use Throwable;

/**
 * Gestion centralisée des exceptions liées aux actions sur les stacks.
 *
 * Cette classe utilise des constructeurs statiques nommés (Named Constructors)
 * pour identifier précisément le contexte de l'erreur SQL ou métier (liaisons, ajouts, etc.).
 */
class StackException extends Exception
{

    /**
     * Leve une exception lorsque la récupération des stacks échoue.
     *
     * @param Throwable|null $previous L'exception d'origine (ex: PDOException) pour le traçage.
     */
    public static function fetchFailed(?Throwable $previous = null): StackException
    {
        return new self("Les stacks n'ont pas pu être récupérer.", 200, $previous);
    }
    /**
     * Leve une exception lorsque l'association des stacks à un projet échoue.
     *
     * @param string $stackName Le nom de la stack.
     * @param Throwable|null $previous L'exception d'origine (ex: PDOException) pour le traçage.
     */
    public static function insertFailed(string $stackName, ?Throwable $previous = null): StackException
    {
        return new self("La stack {$stackName} pas pu être insérées.", 201, $previous);
    }

    /**
     * Leve une exception lorsque la mise à jour (nettoyage ou réinsertion) des stacks d'un projet échoue.
     *
     * @param string $stackName Le titre du projet concerné.
     * @param Throwable|null $previous L'exception d'origine (ex: PDOException) pour le traçage.
     * @return StackException
     */
    public static function updateFailed(string $stackName, ?Throwable $previous = null): StackException
    {
        return new self("La stack '{$stackName}' n'a pas pu être modifiées.", 202, $previous);
    }

    /**
     * Leve une exception lorsque la suppression des liaisons de stacks d'un projet échoue.
     *
     * @param int $stackId L'identifiant du projet concerné.
     * @param Throwable|null $previous L'exception d'origine (ex: PDOException) pour le traçage.
     * @return StackException
     */
    public static function deleteFailed(int $stackId, ?Throwable $previous = null): StackException
    {
        return new self("Impossible de supprimer la stack n°{$stackId}.", 203, $previous);
    }

    /**
     * Leve une exception lorsque l'association des stacks à un projet échoue.
     *
     * @param string $projectTitle Le titre du projet auquel on tentait d'associer les stacks.
     * @param Throwable|null $previous L'exception d'origine (ex: PDOException) pour le traçage.
     * @return StackException
     */
    public static function associateFailed(string $projectTitle, ?Throwable $previous = null): StackException
    {
        return new self("Les stacks du projet '{$projectTitle}' n'ont pas pu être insérées.", 204, $previous);
    }
}