<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Type\EnumTypeEntityType;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity class of the Translation database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the localized translations of the items and recipes etc.',
])]
#[Index(columns: ['locale', 'type', 'name'], name: 'idx_locale_type_name')]
class Translation implements EntityWithId
{
    #[Id]
    #[Column(type: UuidBinaryType::NAME, options: ['comment' => 'The internal id of the translation.'])]
    private UuidInterface $id;

    #[Column(length: 5, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The locale of the translation.',
    ])]
    private string $locale = '';

    #[Column(type: EnumTypeEntityType::NAME, options: ['comment' => 'The type of the translation.'])]
    private string $type = '';

    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The name of the translation.',
    ])]
    private string $name = '';

    #[Column(type: Types::TEXT, length: 65535, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The actual translation.',
    ])]
    private string $value = '';

    #[Column(type: Types::TEXT, length: 65535, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The translated description.',
    ])]
    private string $description = '';

    #[Column(type: Types::BOOLEAN, options: ['comment' => 'Whether this translation is duplicated by the recipe.'])]
    private bool $isDuplicatedByRecipe = false;

    #[Column(type: Types::BOOLEAN, options: ['comment' => 'Whether this translation is duplicated by the machine.'])]
    private bool $isDuplicatedByMachine = false;

    /** @var Collection<int, Combination> */
    #[ManyToMany(targetEntity: Combination::class, mappedBy: 'translations')]
    private Collection $combinations;

    public function __construct()
    {
        $this->combinations = new ArrayCollection();
    }

    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setIsDuplicatedByRecipe(bool $isDuplicatedByRecipe): self
    {
        $this->isDuplicatedByRecipe = $isDuplicatedByRecipe;
        return $this;
    }

    public function getIsDuplicatedByRecipe(): bool
    {
        return $this->isDuplicatedByRecipe;
    }

    public function setIsDuplicatedByMachine(bool $isDuplicatedByMachine): self
    {
        $this->isDuplicatedByMachine = $isDuplicatedByMachine;
        return $this;
    }

    public function getIsDuplicatedByMachine(): bool
    {
        return $this->isDuplicatedByMachine;
    }

    /**
     * @return Collection<int, Combination>
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
