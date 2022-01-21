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
use FactorioItemBrowser\Api\Database\Attribute\IncludeInIdCalculation;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Helper\Validator;

/**
 * The entity representing a product of a recipe.
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
    #[ManyToOne(targetEntity: RecipeData::class, inversedBy: 'products')]
    #[JoinColumn(name: 'recipeDataId', nullable: false)]
    private RecipeData $recipeData;

    #[Id]
    #[Column(name: '`order`', type: CustomTypes::TINYINT, options: [#
        'unsigned' => true,
        'comment' => 'The order of the product in the recipe.',
    ])]
    #[IncludeInIdCalculation]
    private int $order = 0;

    #[ManyToOne(targetEntity: Item::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'itemId', nullable: false)]
    #[IncludeInIdCalculation]
    private Item $item;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The minimal amount of the product in the recipe.',
    ])]
    #[IncludeInIdCalculation]
    private int $amountMin = 0;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The maximal amount of the product in the recipe.',
    ])]
    #[IncludeInIdCalculation]
    private int $amountMax = 0;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The probability of the product in the recipe.',
    ])]
    #[IncludeInIdCalculation]
    private int $probability = 0;

    public function setRecipeData(RecipeData $recipeData): self
    {
        $this->recipeData = $recipeData;
        return $this;
    }

    public function getRecipeData(): RecipeData
    {
        return $this->recipeData;
    }

    public function setOrder(int $order): self
    {
        $this->order = Validator::validateTinyInteger($order);
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
        $this->amountMin = Validator::validateInteger((int) ($amountMin * self::FACTOR_AMOUNT_MIN));
        return $this;
    }

    public function getAmountMin(): float
    {
        return $this->amountMin / self::FACTOR_AMOUNT_MIN;
    }

    public function setAmountMax(float $amountMax): self
    {
        $this->amountMax = Validator::validateInteger((int) ($amountMax * self::FACTOR_AMOUNT_MAX));
        return $this;
    }

    public function getAmountMax(): float
    {
        return $this->amountMax / self::FACTOR_AMOUNT_MAX;
    }

    public function setProbability(float $probability): self
    {
        $this->probability = Validator::validateInteger((int) ($probability * self::FACTOR_AMOUNT_PROBABILITY));
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
        $amount = ($this->getAmountMin() + $this->getAmountMax()) / 2 * $this->getProbability();
        return ((int) ($amount * 1000)) / 1000;
    }
}
