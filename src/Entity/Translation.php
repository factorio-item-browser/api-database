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
use FactorioItemBrowser\Api\Database\Attribute\IncludeInIdCalculation;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Helper\Validator;
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
#[Index(columns: ['locale', 'type', 'name'])]
class Translation implements EntityWithId
{
    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the translation.'])]
    private UuidInterface $id;

    #[Column(length: 5, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The locale of the translation.',
    ])]
    #[IncludeInIdCalculation]
    private string $locale = '';

    #[Column(type: CustomTypes::ENUM_ENTITY_TYPE, options: ['comment' => 'The type of the translation.'])]
    #[IncludeInIdCalculation]
    private string $type = '';

    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The name of the translation.',
    ])]
    #[IncludeInIdCalculation]
    private string $name = '';

    #[Column(type: Types::TEXT, length: 65535, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'comment' => 'The translated label.',
    ])]
    #[IncludeInIdCalculation]
    private string $label = '';

    #[Column(type: Types::TEXT, length: 65535, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'comment' => 'The translated description.',
    ])]
    #[IncludeInIdCalculation]
    private string $description = '';

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
        $this->locale = Validator::validateString($locale, 5);
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
        $this->name = Validator::validateString($name);
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setLabel(string $label): self
    {
        $this->label = Validator::validateText($label);
        return $this;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setDescription(string $description): self
    {
        $this->description = Validator::validateText($description);
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Collection<int, Combination>
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
