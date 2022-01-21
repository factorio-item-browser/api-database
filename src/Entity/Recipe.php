<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Attribute\IncludeInIdCalculation;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Helper\Validator;
use FactorioItemBrowser\Common\Constant\RecipeType;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity representing a recipe to craft items.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the recipes to craft the items.',
])]
#[Index(columns: ['type', 'name'])]
class Recipe implements EntityWithId
{
    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the recipe.'])]
    private UuidInterface $id;

    #[Column(type: CustomTypes::ENUM_RECIPE_TYPE, options: ['comment' => 'The type of the recipe.'])]
    #[IncludeInIdCalculation]
    private string $type = 'recipe';

    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The name of the recipe.',
    ])]
    #[IncludeInIdCalculation]
    private string $name = '';

    #[ManyToOne(targetEntity: Category::class)]
    #[JoinColumn(name: 'categoryId', nullable: true)]
    #[IncludeInIdCalculation]
    private ?Category $category = null;

    #[ManyToOne(targetEntity: RecipeData::class)]
    #[JoinColumn(name: 'normalDataId', nullable: false)]
    #[IncludeInIdCalculation]
    private RecipeData $normalData;

    #[ManyToOne(targetEntity: RecipeData::class)]
    #[JoinColumn(name: 'expensiveDataId', nullable: false)]
    #[IncludeInIdCalculation]
    private RecipeData $expensiveData;

    /** @var Collection<int, Combination> */
    #[ManyToMany(targetEntity: Combination::class, mappedBy: 'recipes')]
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

    public function setCategory(?Category $category): self
    {
        $this->category = $category;
        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setNormalData(RecipeData $normalData): self
    {
        $this->normalData = $normalData;
        return $this;
    }

    public function getNormalData(): RecipeData
    {
        return $this->normalData;
    }

    public function setExpensiveData(RecipeData $expensiveData): self
    {
        $this->expensiveData = $expensiveData;
        return $this;
    }

    public function getExpensiveData(): RecipeData
    {
        return $this->expensiveData;
    }

    /**
     * @return Collection<int, Combination>
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
