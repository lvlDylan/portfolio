<?php

namespace App\Models\Entities;

/**
 * Représente une technologie de la stack technique (ex: PHP, React, Docker).
 *
 * Cette entité lie une technologie à une catégorie précise via CategoryEnum
 * et définit ses attributs visuels pour l'affichage (icône et couleur).
 */
readonly class StackEntity
{
    /** @var int Identifiant unique de la technologie. */
    private int $id;

    /** @var string Nom de la technologie (ex: "Laravel"). */
    private string $name;

    /** @var string Catégorie associée (Frontend, Backend, etc.). */
    private string $category;

    /** @var string|null Nom de la classe d'icône (ex: "fa-brands fa-php"). */
    private ?string $iconName;

    /** @var string|null Code couleur ou nom de classe CSS (ex: "#777BB4"). */
    private ?string $colorName;

    /**
     * @param int $id
     * @param string $name
     * @param string $category
     * @param string|null $iconName
     * @param string|null $colorName
     */
    public function __construct(
        int $id,
        string $name,
        string $category,
        ?string $iconName,
        ?string $colorName
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->iconName = $iconName;
        $this->colorName = $colorName;
    }

    /** @return int */
    public function getId(): int
    {
        return $this->id;
    }

    /** @return string */
    public function getName(): string
    {
        return $this->name;
    }

    /** @return string */
    public function getCategory(): string
    {
        return $this->category;
    }

    /** @return string|null */
    public function getIconName(): ?string
    {
        return $this->iconName;
    }

    /** @return string|null */
    public function getColorName(): ?string
    {
        return $this->colorName;
    }
}