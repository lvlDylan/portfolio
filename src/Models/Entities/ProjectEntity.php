<?php

namespace App\Models\Entities;

/**
 * Représente un projet au sein de l'application.
 *
 * Cette entité est un objet de données immuable (Value Object)
 * servant à transporter les informations d'un projet depuis la base de données.
 */
readonly class ProjectEntity
{
    /** @var int Identifiant unique du projet. */
    private int $id;

    /** @var string Titre du projet. */
    private string $title;

    /** @var string Description complet du projet */
    private string $description;

    /** @var array Tableau de stacks utilisé par le projet */
    private array $stacks;

    /** @var string|null Résumé ou version courte de la description. */
    private ?string $descriptionShortened;

    /** @var string|null Chemin relatif ou URL de l'image d'illustration. */
    private ?string $imageUri;

    /** @var string|null Lien vers le dépôt GitHub du projet. */
    private ?string $githubUrl;

    /**
     * @param int $id
     * @param string $title
     * @param string $description
     * @param array $stacks
     * @param string|null $descriptionShortened
     * @param string|null $imageUri
     * @param string|null $githubUrl
     */
    public function __construct(int $id, string $title, string $description, array $stacks, ?string $descriptionShortened, ?string $imageUri, ?string $githubUrl)
    {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->stacks = $stacks;
        $this->descriptionShortened = $descriptionShortened;
        $this->imageUri = $imageUri;
        $this->githubUrl = $githubUrl;
    }

    /** @return int */
    public function getId(): int
    {
        return $this->id;
    }

    /** @return string */
    public function getTitle(): string
    {
        return $this->title;
    }

    /** @return string */
    public function getDescription(): string
    {
        return $this->description;
    }

    /** @return array */
    public function getStacks(): array
    {
        return $this->stacks;
    }

    /** @return string|null */
    public function getDescriptionShortened(): ?string
    {
        return $this->descriptionShortened;
    }

    /** @return string|null */
    public function getImageUri(): ?string
    {
        return $this->imageUri;
    }

    /** @return string|null */
    public function getGithubUrl(): ?string
    {
        return $this->githubUrl;
    }
}