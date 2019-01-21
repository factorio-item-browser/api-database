<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

/**
 * The entity class of the recipe product database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RecipeProduct
{
    /**
     * The factor of the minimal amount.
     */
    protected const FACTOR_AMOUNT_MIN = 1000;

    /**
     * The factor of the maximal amount.
     */
    protected const FACTOR_AMOUNT_MAX = 1000;

    /**
     * The factor of the probability.
     */
    protected const FACTOR_AMOUNT_PROBABILITY = 1000;

    /**
     * The recipe of the result.
     * @var Recipe
     */
    protected $recipe;

    /**
     * The item of the result.
     * @var Item
     */
    protected $item;

    /**
     * The minimal amount of the product in the recipe.
     * @var int
     */
    protected $amountMin = 0;

    /**
     * The maximal amount of the product in the recipe.
     * @var int
     */
    protected $amountMax = 0;

    /**
     * The probability of the product in the recipe.
     * @var int
     */
    protected $probability = 0;

    /**
     * The order of the product in the recipe.
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
     * Sets the recipe of the result.
     * @param Recipe $recipe
     * @return $this Implementing fluent interface.
     */
    public function setRecipe(Recipe $recipe)
    {
        $this->recipe = $recipe;
        return $this;
    }

    /**
     * Returns the recipe of the result.
     * @return Recipe
     */
    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    /**
     * Sets the item of the result.
     * @param Item $item
     * @return $this Implementing fluent interface.
     */
    public function setItem(Item $item)
    {
        $this->item = $item;
        return $this;
    }

    /**
     * Returns the item of the result.
     * @return Item
     */
    public function getItem(): Item
    {
        return $this->item;
    }

    /**
     * Sets the minimal amount of the product in the recipe.
     * @param float $amountMin
     * @return $this Implementing fluent interface.
     */
    public function setAmountMin(float $amountMin)
    {
        $this->amountMin = (int) ($amountMin * self::FACTOR_AMOUNT_MIN);
        return $this;
    }

    /**
     * Returns the minimal amount of the product in the recipe.
     * @return float
     */
    public function getAmountMin(): float
    {
        return $this->amountMin / self::FACTOR_AMOUNT_MIN;
    }

    /**
     * Sets the maximal amount of the product in the recipe.
     * @param float $amountMax
     * @return $this Implementing fluent interface.
     */
    public function setAmountMax(float $amountMax)
    {
        $this->amountMax = (int) ($amountMax * self::FACTOR_AMOUNT_MAX);
        return $this;
    }

    /**
     * Returns the maximal amount of the product in the recipe.
     * @return float
     */
    public function getAmountMax(): float
    {
        return $this->amountMax / self::FACTOR_AMOUNT_MAX;
    }

    /**
     * Sets the probability of the product in the recipe.
     * @param float $probability
     * @return $this Implementing fluent interface.
     */
    public function setProbability(float $probability)
    {
        $this->probability = (int) ($probability * self::FACTOR_AMOUNT_PROBABILITY);
        return $this;
    }

    /**
     * Returns the probability of the product in the recipe.
     * @return float
     */
    public function getProbability(): float
    {
        return $this->probability / self::FACTOR_AMOUNT_PROBABILITY;
    }

    /**
     * Returns the amount calculated from the other amount values.
     * @return float
     */
    public function getAmount(): float
    {
        return ($this->getAmountMin() + $this->getAmountMax()) / 2 * $this->getProbability();
    }

    /**
     * Sets the order of the product in the recipe.
     * @param int $order
     * @return $this Implementing fluent interface.
     */
    public function setOrder(int $order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Returns the order of the product in the recipe.
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }
}
