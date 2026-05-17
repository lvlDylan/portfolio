<?php

namespace App\Models\Api;

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
}