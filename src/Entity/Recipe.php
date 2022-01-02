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
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Type\EnumTypeRecipeMode;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity class of the recipe database table.
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
#[Index(columns: ['name'])]
class Recipe implements EntityWithId
{
    private const FACTOR_CRAFTING_TIME = 1000;

    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the recipe.'])]
    private UuidInterface $id;

    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The name of the recipe.',
    ])]
    private string $name = '';

    #[Column(type: EnumTypeRecipeMode::NAME, options: ['comment' => 'The mode of the recipe.'])]
    private string $mode = '';

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The required time in milliseconds to craft the recipe.',
    ])]
    private int $craftingTime = 0;

    #[ManyToOne(targetEntity: CraftingCategory::class)]
    #[JoinColumn(name: 'craftingCategoryId', nullable: false)]
    private CraftingCategory $craftingCategory;

    /** @var Collection<int, RecipeIngredient> */
    #[OneToMany(mappedBy: 'recipe', targetEntity: RecipeIngredient::class)]
    #[OrderBy(['order' => 'ASC'])]
    private Collection $ingredients;

    /** @var Collection<int, RecipeProduct> */
    #[OneToMany(mappedBy: 'recipe', targetEntity: RecipeProduct::class)]
    #[OrderBy(['order' => 'ASC'])]
    private Collection $products;

    /** @var Collection<int, Combination> */
    #[ManyToMany(targetEntity: Combination::class, mappedBy: 'recipes')]
    private Collection $combinations;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->products = new ArrayCollection();
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
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setCraftingTime(float $craftingTime): self
    {
        $this->craftingTime = (int) ($craftingTime * self::FACTOR_CRAFTING_TIME);
        return $this;
    }

    public function getCraftingTime(): float
    {
        return $this->craftingTime / self::FACTOR_CRAFTING_TIME;
    }

    public function setCraftingCategory(CraftingCategory $craftingCategory): self
    {
        $this->craftingCategory = $craftingCategory;
        return $this;
    }

    public function getCraftingCategory(): CraftingCategory
    {
        return $this->craftingCategory;
    }

    /**
     * @return Collection<int, RecipeIngredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    /**
     * @return Collection<int, RecipeProduct>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @return Collection<int, Combination>
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
