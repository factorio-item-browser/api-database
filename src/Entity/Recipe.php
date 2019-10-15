<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity class of the recipe database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Recipe
{
    /**
     * The factor of the crafting time.
     */
    protected const FACTOR_CRAFTING_TIME = 1000;

    /**
     * The internal id of the recipe.
     * @var UuidInterface
     */
    protected $id;

    /**
     * The name of the recipe.
     * @var string
     */
    protected $name = '';

    /**
     * The mode of the recipe.
     * @var string
     */
    protected $mode = '';

    /**
     * The required time in milliseconds to craft the recipe.
     * @var int
     */
    protected $craftingTime = 0;

    /**
     * The crafting category of the recipe.
     * @var CraftingCategory
     */
    protected $craftingCategory;

    /**
     * The ingredients of the recipe.
     * @var Collection|RecipeIngredient[]
     */
    protected $ingredients;

    /**
     * The products of the recipe.
     * @var Collection|RecipeProduct[]
     */
    protected $products;

    /**
     * The combinations which are adding the recipe.
     * @var Collection|Combination[]
     */
    protected $combinations;

    /**
     * Initializes the entity.
     */
    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->combinations = new ArrayCollection();
    }

    /**
     * Sets the internal id of the recipe.
     * @param UuidInterface $id
     * @return $this Implementing fluent interface.
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the internal id of the recipe.
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Sets the name of the recipe.
     * @param string $name
     * @return $this Implementing fluent interface.
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the recipe.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the mode of the recipe.
     * @param string $mode
     * @return $this Implementing fluent interface.
     */
    public function setMode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Returns the mode of the recipe.
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Sets the required time in seconds to craft the recipe.
     * @param float $craftingTime
     * @return $this Implementing fluent interface.
     */
    public function setCraftingTime(float $craftingTime): self
    {
        $this->craftingTime = (int) ($craftingTime * self::FACTOR_CRAFTING_TIME);
        return $this;
    }

    /**
     * Returns the required time in seconds to craft the recipe.
     * @return float
     */
    public function getCraftingTime(): float
    {
        return $this->craftingTime / self::FACTOR_CRAFTING_TIME;
    }

    /**
     * Sets the crafting category of the recipe.
     * @param CraftingCategory $craftingCategory
     * @return $this
     */
    public function setCraftingCategory(CraftingCategory $craftingCategory): self
    {
        $this->craftingCategory = $craftingCategory;
        return $this;
    }

    /**
     * Returns the crafting category of the recipe.
     * @return CraftingCategory
     */
    public function getCraftingCategory(): CraftingCategory
    {
        return $this->craftingCategory;
    }

    /**
     * Returns the ingredients of the recipe.
     * @return Collection|RecipeIngredient[]
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    /**
     * Returns the products of the recipe.
     * @return Collection|RecipeProduct[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * Returns the combinations adding the recipe.
     * @return Collection|Combination[]
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
