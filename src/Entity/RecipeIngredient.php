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
 * The entity class of the recipe ingredient database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collate' => 'utf8mb4_bin',
    'comment' => 'The table holding the ingredients for the recipes.',
])]
class RecipeIngredient
{
    private const FACTOR_AMOUNT = 1000;

    #[Id]
    #[ManyToOne(targetEntity: Recipe::class, inversedBy: 'ingredients')]
    #[JoinColumn(name: 'recipeId', nullable: false)]
    private Recipe $recipe;

    #[Id]
    #[Column(name: '`order`', type: 'tinyint', options: [#
        'unsigned' => true,
        'comment' => 'The order of the ingredient in the recipe.',
    ])]
    private int $order = 0;

    #[ManyToOne(targetEntity: Item::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'itemId', nullable: false)]
    private Item $item;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The amount required for the recipe.',
    ])]
    private int $amount = 0;

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

    public function setAmount(float $amount): self
    {
        $this->amount = (int) ($amount * self::FACTOR_AMOUNT);
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount / self::FACTOR_AMOUNT;
    }
}
