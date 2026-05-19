<?php

namespace App\Models\Api;

use App\Exceptions\StackException;
use App\Models\Entities\StackEntity;
use App\Services\Database;
use PDO;
use PDOException;

/**
 * Class StackModel
 * * Gère l'accès aux données de la table 'stacks' (compétences techniques).
 * Permet de récupérer les technologies classées par catégories pour l'affichage API.
 * * @package App\Models\Api
 */
class StackModel
{
    /**
     * @var PDO|null Instance de connexion à la base de données.
     */
    private ?PDO $database;

    /**
     * StackModel constructor.
     * * Initialise la connexion via le Singleton Database.
     */
    public function __construct()
    {
        $this->database = Database::getInstance();
    }

    /**
     * Récupère l'intégralité des stacks techniques en base de données.
     * * @return array<int, array{
     * id: int,
     * name: string,
     * category: string,
     * icon_name: string,
     * color_name: string
     * }> Retourne la liste des stacks ou false en cas d'erreur SQL.
     * @throws StackException Levée si la récupération des stacks échoue.
     */
    public function findAll(): array
    {
        try {
            return $this->database->query("SELECT * FROM stacks")->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw StackException::fetchFailed($e);
        }
    }

    /**
     * Recherche une stack en base de données par son nom.
     *
     * @param string $name Le nom de la stack à rechercher (ex: "java", "c").
     * @return array|null Retourne les données de la stack sous forme de tableau associatif ou false si aucune stack ne correspond.
     */
    public function findByName(string $name): array | null
    {
        try {
            $sql = "SELECT * FROM stacks WHERE name = :name";
            $stmt = $this->database->prepare($sql);
            $stmt->execute(["name" => $name]);
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        } catch (PDOException $e) {
            return null;
        }
    }


    /**
     * Insère une stack en base de données.
     *
     * @param StackEntity $stackEntity L'entité de la stack à insérer.
     * @throws StackException Lancée si l'insertion des stacks échoue.
 */
    public function insert(StackEntity $stackEntity): void
    {
        try {
            $this->database->beginTransaction();
            $sql = "INSERT INTO stacks (name, category, icon_name, color_name) VALUES (:name, :category, :icon_name, :color_name)";
            $stmt = $this->database->prepare($sql);
            $stmt->execute([
                "name" => $stackEntity->getName(),
                "category" => $stackEntity->getCategory(),
                "icon_name" => $stackEntity->getIconName(),
                "color_name" => $stackEntity->getColorName(),
            ]);
            $this->database->commit();
        } catch (PDOException $e) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }

            throw StackException::insertFailed($stackEntity->getName(), $e);
        }
    }

    /**
     * Met à jour une stack existante.
     * Elle écrase les anciennes valeurs de la stack.
     *
     * @param StackEntity $stackEntity L'entité de la stack contenant l'ID et les données modifiées.
     * @throws StackException Levée si la mise à jour de la stack échoue.
     */
    public function update(StackEntity $stackEntity): void
    {

        try {
            $this->database->beginTransaction();

            $sql = "UPDATE stacks SET name=:name, category=:category, icon_name=:icon_name, color_name=:color_name WHERE id=:id";
            $stmt = $this->database->prepare($sql);
            $stmt->execute([
                "id" => $stackEntity->getId(),
                "name" => $stackEntity->getName(),
                "category" => $stackEntity->getCategory(),
                "icon_name" => $stackEntity->getIconName(),
                "color_name" => $stackEntity->getColorName(),
            ]);
            $this->database->commit();
        } catch (PDOException|StackException $e) {
            if ($this->database->inTransaction()) {
                $this->database->rollBack();
            }

            throw StackException::updateFailed($stackEntity->getName(), $e);
        }


    }

    /**
     * Supprime la stack définit par l'id en base.
     * @param int $id L'identifiant en base de la stack.
     * @throws StackException Levée si la suppresion de la stack échoue.
     */
    public function delete(int $id): void
    {
        try {
            $sql = "DELETE FROM stacks WHERE id=:id";
            $stmt = $this->database->prepare($sql);
            $stmt->execute(["id" => $id]);
        } catch (PDOException $e) {
            throw StackException::deleteFailed($id, $e);
        }
    }
}