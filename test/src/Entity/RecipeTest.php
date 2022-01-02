<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\CraftingCategory;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the Recipe class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\Recipe
 */
class RecipeTest extends TestCase
{
    private function createInstance(): Recipe
    {
        return new Recipe();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getIngredients());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getProducts());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getCombinations());
    }

    public function testSetAndGetId(): void
    {
        $id = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());
    }

    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());
    }

    public function testSetAndGetMode(): void
    {
        $mode = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setMode($mode));
        $this->assertSame($mode, $instance->getMode());
    }

    public function testSetAndGetCraftingTime(): void
    {
        $craftingTime = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setCraftingTime($craftingTime));
        $this->assertSame($craftingTime, $instance->getCraftingTime());
    }

    public function testSetAndGetCraftingCategory(): void
    {
        $craftingCategory = new CraftingCategory();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setCraftingCategory($craftingCategory));
        $this->assertSame($craftingCategory, $instance->getCraftingCategory());
    }
}
