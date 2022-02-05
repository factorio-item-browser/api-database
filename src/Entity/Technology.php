<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Attribute\IncludeCollectionPropertiesInIdCalculation;
use FactorioItemBrowser\Api\Database\Attribute\IncludeInIdCalculation;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Helper\Validator;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity representing a technology.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the technologies.',
])]
class Technology implements EntityWithId
{
    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the technology.'])]
    private UuidInterface $id;

    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The name of the technology.',
    ])]
    #[IncludeInIdCalculation]
    private string $name = '';

    #[ManyToOne(targetEntity: TechnologyData::class)]
    #[JoinColumn(name: 'normalDataId', nullable: false)]
    #[IncludeInIdCalculation]
    private TechnologyData $normalData;

    #[ManyToOne(targetEntity: TechnologyData::class)]
    #[JoinColumn(name: 'expensiveDataId', nullable: false)]
    #[IncludeInIdCalculation]
    private TechnologyData $expensiveData;

    /** @var Collection<int, Technology> */
    #[ManyToMany(targetEntity: Technology::class)]
    #[JoinTable(name: 'TechnologyPrerequisite')]
    #[JoinColumn(name: 'technologyId', nullable: false)]
    #[InverseJoinColumn(name: 'prerequisiteId', nullable: false)]
    #[IncludeInIdCalculation(['name'])]
    private Collection $prerequisites;

    /** @var Collection<int, Recipe> */
    #[ManyToMany(targetEntity: Recipe::class)]
    #[JoinTable(name: 'TechnologyRecipeUnlock')]
    #[JoinColumn(name: 'technologyId', nullable: false)]
    #[InverseJoinColumn(name: 'recipeId', nullable: false)]
    #[IncludeInIdCalculation(['type', 'name'])]
    private Collection $recipeUnlocks;

    /** @var Collection<int, Combination> */
    #[ManyToMany(targetEntity: Combination::class, mappedBy: 'technologies')]
    private Collection $combinations;

    public function __construct()
    {
        $this->prerequisites = new ArrayCollection();
        $this->recipeUnlocks = new ArrayCollection();
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

    public function setName(string $name): self
    {
        $this->name = Validator::validateString($name);
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setNormalData(TechnologyData $normalData): self
    {
        $this->normalData = $normalData;
        return $this;
    }

    public function getNormalData(): TechnologyData
    {
        return $this->normalData;
    }

    public function setExpensiveData(TechnologyData $expensiveData): self
    {
        $this->expensiveData = $expensiveData;
        return $this;
    }

    public function getExpensiveData(): TechnologyData
    {
        return $this->expensiveData;
    }

    /**
     * @return Collection<int, Technology>
     */
    public function getPrerequisites(): Collection
    {
        return $this->prerequisites;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipeUnlocks(): Collection
    {
        return $this->recipeUnlocks;
    }

    /**
     * @return Collection<int, Combination>
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
