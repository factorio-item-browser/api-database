<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;

/**
 * The entity class of the recipe product database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the products for the recipes.',
])]
class RecipeProduct
{
    private const FACTOR_AMOUNT_MIN = 1000;
    private const FACTOR_AMOUNT_MAX = 1000;
    private const FACTOR_AMOUNT_PROBABILITY = 1000;

    #[Id]
    #[ManyToOne(targetEntity: Recipe::class, inversedBy: 'products')]
    #[JoinColumn(name: 'recipeId', nullable: false)]
    private Recipe $recipe;

    #[Id]
    #[Column(name: '`order`', type: 'tinyint', options: [#
        'unsigned' => true,
        'comment' => 'The order of the product in the recipe.',
    ])]
    private int $order = 0;

    #[ManyToOne(targetEntity: Item::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'itemId', nullable: false)]
    private Item $item;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The minimal amount of the product in the recipe.',
    ])]
    private int $amountMin = 0;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The maximal amount of the product in the recipe.',
    ])]
    private int $amountMax = 0;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The probability of the product in the recipe.',
    ])]
    private int $probability = 0;

    public function setRecipe(Recipe $recipe): self
    {
        $this->recipe = $recipe;
        return $this;
    }

    public function getRecipe(): Recipe
    {
        return $this->recipe;
    }

    public function setOrder(int $order): self
    {
        $this->order = $order;
        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setItem(Item $item): self
    {
        $this->item = $item;
        return $this;
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function setAmountMin(float $amountMin): self
    {
        $this->amountMin = (int) ($amountMin * self::FACTOR_AMOUNT_MIN);
        return $this;
    }

    public function getAmountMin(): float
    {
        return $this->amountMin / self::FACTOR_AMOUNT_MIN;
    }

    public function setAmountMax(float $amountMax): self
    {
        $this->amountMax = (int) ($amountMax * self::FACTOR_AMOUNT_MAX);
        return $this;
    }

    public function getAmountMax(): float
    {
        return $this->amountMax / self::FACTOR_AMOUNT_MAX;
    }

    public function setProbability(float $probability): self
    {
        $this->probability = (int) ($probability * self::FACTOR_AMOUNT_PROBABILITY);
        return $this;
    }

    public function getProbability(): float
    {
        return $this->probability / self::FACTOR_AMOUNT_PROBABILITY;
    }

    /**
     * Returns the amount calculated from the other amount values.
     */
    public function getAmount(): float
    {
        return ($this->getAmountMin() + $this->getAmountMax()) / 2 * $this->getProbability();
    }
}
