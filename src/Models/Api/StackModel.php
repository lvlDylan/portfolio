<?php

namespace App\Models\Api;

use App\Models\Entities\StackEntity;
use App\Services\Database;
use PDO;

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
     * }>|false Retourne la liste des stacks ou false en cas d'erreur SQL.
     */
    public function findAll(): array | false
    {
        return $this->database->query("SELECT * FROM stacks")->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Recherche une stack en base de données par son nom.
     *
     * @param string $name Le nom de la stack à rechercher (ex: "java", "c").
     * @return array|false Retourne les données de la stack sous forme de tableau associatif ou false si aucune stack ne correspond.
     */
    public function findByName(string $name): array | false
    {
        $stmt = $this->database->prepare("SELECT * FROM stacks WHERE name=:name");
        $stmt->execute(["name" => $name]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Insère une stack en base de données.
     *
     * @param StackEntity $stackEntity L'entité de la stack à insérer.
     * @return bool True si la stack a été inséré avec succès, false sinon.
     */
    public function insert(StackEntity $stackEntity): bool
    {
        $stmt = $this->database->prepare("INSERT INTO stacks (name, category, icon_name, color_name) VALUES (:name, :category, :icon_name, :color_name)");
        return $stmt->execute([
            "name" => $stackEntity->getName(),
            "category" => $stackEntity->getCategory(),
            "icon_name" => $stackEntity->getIconName(),
            "color_name" => $stackEntity->getColorName(),
        ]);
    }

    /**
     * Met à jour une stack existante.
     * Elle écrase les anciennes valeurs de la stack.
     *
     * @param StackEntity $stackEntity L'entité de la stack contenant l'ID et les données modifiées.
     * @return bool True si la mise à jour a réussi, false sinon.
     */
    public function update(StackEntity $stackEntity): bool
    {
        $this->database->beginTransaction();
        $delStatement = $this->database->prepare("DELETE FROM stacks WHERE name=:name");
        $result = $delStatement->execute(["name" => $stackEntity->getName()]);

        if (!$result) {
            $this->database->rollBack();
            return false;
        }

        if ($this->insert($stackEntity)) {
            $this->database->commit();
            return true;
        } else {
            $this->database->rollBack();
            return false;
        }
    }

    /**
     * Supprime la stack définit par l'id en base.
     * @param int $id L'identifiant en base de la stack.
     * @return bool True si la suppression de la stack a réussi, false sinon.
     */
    public function delete(int $id): bool
    {
        $stmt = $this->database->prepare("DELETE FROM stacks WHERE id=:id");
        return $stmt->execute(["id" => $id]);
    }
}