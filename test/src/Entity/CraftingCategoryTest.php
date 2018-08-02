<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\CraftingCategory;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the CraftingCategory class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\CraftingCategory
 */
class CraftingCategoryTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     * @covers ::getMachines
     * @covers ::getRecipes
     */
    public function testConstruct()
    {
        $craftingCategory = new CraftingCategory('abc');

        $this->assertSame(0, $craftingCategory->getId());
        $this->assertSame('abc', $craftingCategory->getName());
        $this->assertInstanceOf(ArrayCollection::class, $craftingCategory->getMachines());
        $this->assertInstanceOf(ArrayCollection::class, $craftingCategory->getRecipes());
    }

    /**
     * Tests setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId()
    {
        $craftingCategory = new CraftingCategory('foo');

        $id = 42;
        $this->assertSame($craftingCategory, $craftingCategory->setId($id));
        $this->assertSame($id, $craftingCategory->getId());
    }

    /**
     * Tests setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName()
    {
        $craftingCategory = new CraftingCategory('foo');

        $name = 'abc';
        $this->assertSame($craftingCategory, $craftingCategory->setName($name));
        $this->assertSame($name, $craftingCategory->getName());
    }
}
