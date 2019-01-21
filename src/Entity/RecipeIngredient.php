<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

/**
 * The entity class of the recipe ingredient database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RecipeIngredient
{
    /**
     * The factor used for the amount.
     */
    protected const FACTOR_AMOUNT = 1000;

    /**
     * The recipe of the ingredient.
     * @var Recipe
     */
    protected $recipe;

    /**
     * The item of the ingredient.
     * @var Item
     */
    protected $item;

    /**
     * The amount required for the recipe.
     * @var int
     */
    protected $amount = 0;

    /**
     * The order of the ingredient in the recipe.
     * @var int
     */
    protected $order = 0;

    /**
     * Initializes the entity.
     * @param Recipe $recipe
     * @param Item $item
     */
    public function __construct(Recipe $recipe, Item $item)
    {
        $this->recipe = $recipe;
        $this->item = $item;
    }

    /**
     * Sets the recipe of the ingredient.
     * @param Recipe $recipe
     * @return $this Implementing fluent interface.
     */
    public function setRecipe(Recipe $recipe)
    {
        $this->recipe = $recipe;
        return $this;
    }

    /**
     * Returns the recipe of the ingredient.
     * @return Recipe
     */
    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    /**
     * Sets the item of the ingredient.
     * @param Item $item
     * @return $this Implementing fluent interface.
     */
    public function setItem(Item $item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Returns the item of the ingredient.
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * Sets the amount required for the recipe.
     * @param float $amount
     * @return $this Implementing fluent interface.
     */
    public function setAmount(float $amount)
    {
        $this->amount = (int) ($amount * self::FACTOR_AMOUNT);
        return $this;
    }

    /**
     * Returns the amount required for the recipe.
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount / self::FACTOR_AMOUNT;
    }

    /**
     * Sets the order of the ingredient in the recipe.
     * @param int $order
     * @return $this Implementing fluent interface.
     */
    public function setOrder(int $order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Returns the order of the ingredient in the recipe.
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }
}
