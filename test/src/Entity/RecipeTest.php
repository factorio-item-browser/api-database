<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\CraftingCategory;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Recipe class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\Recipe
 */
class RecipeTest extends TestCase
{
    /**
     * Tests the constructing
     * @covers ::__construct
     * @covers ::getIngredients()
     * @covers ::getCombinations
     * @covers ::getProducts()
     */
    public function testConstruct(): void
    {
        $recipe = new Recipe();

        $this->assertInstanceOf(ArrayCollection::class, $recipe->getIngredients());
        $this->assertInstanceOf(ArrayCollection::class, $recipe->getProducts());
        $this->assertInstanceOf(ArrayCollection::class, $recipe->getCombinations());
    }

    /**
     * Tests setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);
        $recipe = new Recipe();

        $this->assertSame($recipe, $recipe->setId($id));
        $this->assertSame($id, $recipe->getId());
    }

    /**
     * Tests setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $recipe = new Recipe();

        $this->assertSame($recipe, $recipe->setName($name));
        $this->assertSame($name, $recipe->getName());
    }

    /**
     * Tests setting and getting the mode.
     * @covers ::getMode
     * @covers ::setMode
     */
    public function testSetAndGetMode(): void
    {
        $mode = 'abc';
        $recipe = new Recipe();

        $this->assertSame($recipe, $recipe->setMode($mode));
        $this->assertSame($mode, $recipe->getMode());
    }

    /**
     * Tests setting and getting the craftingTime.
     * @covers ::getCraftingTime
     * @covers ::setCraftingTime
     */
    public function testSetAndGetCraftingTime(): void
    {
        $craftingTime = 13.37;
        $recipe = new Recipe();

        $this->assertSame($recipe, $recipe->setCraftingTime($craftingTime));
        $this->assertSame($craftingTime, $recipe->getCraftingTime());
    }

    /**
     * Tests setting and getting the craftingCategory.
     * @covers ::getCraftingCategory
     * @covers ::setCraftingCategory
     */
    public function testSetAndGetCraftingCategory(): void
    {
        /* @var CraftingCategory&MockObject $craftingCategory */
        $craftingCategory = $this->createMock(CraftingCategory::class);
        $recipe = new Recipe();

        $this->assertSame($recipe, $recipe->setCraftingCategory($craftingCategory));
        $this->assertSame($craftingCategory, $recipe->getCraftingCategory());
    }
}
