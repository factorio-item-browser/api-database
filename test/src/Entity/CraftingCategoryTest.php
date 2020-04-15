<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\CraftingCategory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

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
    public function testConstruct(): void
    {
        $craftingCategory = new CraftingCategory();

        $this->assertInstanceOf(ArrayCollection::class, $craftingCategory->getMachines());
        $this->assertInstanceOf(ArrayCollection::class, $craftingCategory->getRecipes());
    }

    /**
     * Tests the setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);
        $craftingCategory = new CraftingCategory();

        $this->assertSame($craftingCategory, $craftingCategory->setId($id));
        $this->assertSame($id, $craftingCategory->getId());
    }

    /**
     * Tests the setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $craftingCategory = new CraftingCategory();

        $this->assertSame($craftingCategory, $craftingCategory->setName($name));
        $this->assertSame($name, $craftingCategory->getName());
    }
}
