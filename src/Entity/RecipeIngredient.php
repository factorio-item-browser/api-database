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
    protected Recipe $recipe;

    /**
     * The order of the ingredient in the recipe.
     * @var int
     */
    protected int $order = 0;

    /**
     * The item of the ingredient.
     * @var Item
     */
    protected Item $item;

    /**
     * The amount required for the recipe.
     * @var int
     */
    protected int $amount = 0;

    /**
     * Sets the recipe of the ingredient.
     * @param Recipe $recipe
     * @return $this Implementing fluent interface.
     */
    public function setRecipe(Recipe $recipe): self
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
     * Sets the order of the ingredient in the recipe.
     * @param int $order
     * @return $this Implementing fluent interface.
     */
    public function setOrder(int $order): self
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

    /**
     * Sets the item of the ingredient.
     * @param Item $item
     * @return $this Implementing fluent interface.
     */
    public function setItem(Item $item): self
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
    public function setAmount(float $amount): self
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
}
