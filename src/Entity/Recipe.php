<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;

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
     * @var int|null
     */
    protected $id;

    /**
     * The mod combinations which are adding the recipe.
     * @var Collection|ModCombination[]
     */
    protected $modCombinations;

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
     * Initializes the entity.
     * @param string $name
     * @param string $mode
     * @param CraftingCategory $craftingCategory
     */
    public function __construct(string $name, string $mode, CraftingCategory $craftingCategory)
    {
        $this->name = $name;
        $this->mode = $mode;
        $this->craftingCategory = $craftingCategory;
        $this->modCombinations = new ArrayCollection();
        $this->ingredients = new ArrayCollection();
        $this->products = new ArrayCollection();
    }

    /**
     * Sets the internal id of the recipe.
     * @param int $id
     * @return $this Implementing fluent interface.
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the internal id of the recipe.
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * Returns the mod combinations adding the recipe.
     * @return Collection|ModCombination[]
     */
    public function getModCombinations()
    {
        return $this->modCombinations;
    }

    /**
     * Sets the name of the recipe.
     * @param string $name
     * @return $this Implementing fluent interface.
     */
    public function setName(string $name)
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
    public function setMode(string $mode)
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
    public function setCraftingTime(float $craftingTime)
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
    public function setCraftingCategory(CraftingCategory $craftingCategory)
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
     * Returns the ordered ingredients of the recipe, in case the ingredients are not already ordered.
     * @return Collection|RecipeIngredient[]
     */
    public function getOrderedIngredients(): Collection
    {
        return $this->ingredients->matching(Criteria::create()->orderBy(['order' => Criteria::ASC]));
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
     * Returns the ordered products of the recipe, in case the products are not already ordered.
     * @return Collection|RecipeProduct[]
     */
    public function getOrderedProducts(): Collection
    {
        return $this->products->matching(Criteria::create()->orderBy(['order' => Criteria::ASC]));
    }
}
