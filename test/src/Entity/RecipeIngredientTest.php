<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the RecipeIngredient class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\RecipeIngredient
 */
class RecipeIngredientTest extends TestCase
{
    /**
     * Tests the setting and getting the recipe.
     * @covers ::getRecipe
     * @covers ::setRecipe
     */
    public function testSetAndGetRecipe(): void
    {
        /* @var Recipe&MockObject $recipe */
        $recipe = $this->createMock(Recipe::class);
        $ingredient = new RecipeIngredient();

        $this->assertSame($ingredient, $ingredient->setRecipe($recipe));
        $this->assertSame($recipe, $ingredient->getRecipe());
    }

    /**
     * Tests the setting and getting the order.
     * @covers ::getOrder
     * @covers ::setOrder
     */
    public function testSetAndGetOrder(): void
    {
        $order = 42;
        $ingredient = new RecipeIngredient();

        $this->assertSame($ingredient, $ingredient->setOrder($order));
        $this->assertSame($order, $ingredient->getOrder());
    }

    /**
     * Tests the setting and getting the item.
     * @covers ::getItem
     * @covers ::setItem
     */
    public function testSetAndGetItem(): void
    {
        /* @var Item&MockObject $item */
        $item = $this->createMock(Item::class);
        $ingredient = new RecipeIngredient();

        $this->assertSame($ingredient, $ingredient->setItem($item));
        $this->assertSame($item, $ingredient->getItem());
    }

    /**
     * Tests the setting and getting the amount.
     * @covers ::getAmount
     * @covers ::setAmount
     */
    public function testSetAndGetAmount(): void
    {
        $amount = 13.37;
        $ingredient = new RecipeIngredient();

        $this->assertSame($ingredient, $ingredient->setAmount($amount));
        $this->assertSame($amount, $ingredient->getAmount());
    }
}
