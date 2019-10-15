<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Mod;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the ModCombination class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\Combination
 */
class ModCombinationTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     * @covers ::getIcons
     * @covers ::getItems
     * @covers ::getMachines
     * @covers ::getRecipes
     * @covers ::getTranslations
     */
    public function testConstruct(): void
    {
        $mod = new Mod('abc');
        $name = 'def';
        $modCombination = new Combination($mod, $name);

        $this->assertSame(0, $modCombination->getId());
        $this->assertSame($mod, $modCombination->getMod());
        $this->assertSame($name, $modCombination->getName());
        $this->assertSame([], $modCombination->getOptionalModIds());
        $this->assertSame(0, $modCombination->getOrder());
        $this->assertInstanceOf(ArrayCollection::class, $modCombination->getItems());
        $this->assertInstanceOf(ArrayCollection::class, $modCombination->getRecipes());
        $this->assertInstanceOf(ArrayCollection::class, $modCombination->getMachines());
        $this->assertInstanceOf(ArrayCollection::class, $modCombination->getTranslations());
        $this->assertInstanceOf(ArrayCollection::class, $modCombination->getIcons());
    }

    /**
     * Tests setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        $modCombination = new Combination(new Mod('foo'), 'bar');

        $id = 42;
        $this->assertSame($modCombination, $modCombination->setId($id));
        $this->assertSame($id, $modCombination->getId());
    }

    /**
     * Tests setting and getting the mod.
     * @covers ::getMod
     * @covers ::setMod
     */
    public function testSetAndGetMod(): void
    {
        $modCombination = new Combination(new Mod('foo'), 'bar');

        $mod = new Mod('abc');
        $this->assertSame($modCombination, $modCombination->setMod($mod));
        $this->assertSame($mod, $modCombination->getMod());
    }

    /**
     * Tests setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $modCombination = new Combination(new Mod('foo'), 'bar');

        $name = 'abc';
        $this->assertSame($modCombination, $modCombination->setName($name));
        $this->assertSame($name, $modCombination->getName());
    }

    /**
     * Tests setting and getting the optionalModIds.
     * @covers ::getOptionalModIds
     * @covers ::setOptionalModIds
     */
    public function testSetAndGetOptionalModIds(): void
    {
        $modCombination = new Combination(new Mod('foo'), 'bar');

        $optionalModIds = [42, 1337];
        $this->assertSame($modCombination, $modCombination->setOptionalModIds($optionalModIds));
        $this->assertSame($optionalModIds, $modCombination->getOptionalModIds());
    }

    /**
     * Tests setting and getting the order.
     * @covers ::getOrder
     * @covers ::setOrder
     */
    public function testSetAndGetOrder(): void
    {
        $modCombination = new Combination(new Mod('foo'), 'bar');

        $order = 42;
        $this->assertSame($modCombination, $modCombination->setOrder($order));
        $this->assertSame($order, $modCombination->getOrder());
    }
}
