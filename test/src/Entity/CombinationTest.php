<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Combination class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\Combination
 */
class CombinationTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     * @covers ::getMods
     * @covers ::getItems
     * @covers ::getMachines
     * @covers ::getRecipes
     * @covers ::getIcons
     * @covers ::getTranslations
     */
    public function testConstruct(): void
    {
        $combination = new Combination();

        $this->assertInstanceOf(ArrayCollection::class, $combination->getMods());
        $this->assertInstanceOf(ArrayCollection::class, $combination->getItems());
        $this->assertInstanceOf(ArrayCollection::class, $combination->getMachines());
        $this->assertInstanceOf(ArrayCollection::class, $combination->getRecipes());
        $this->assertInstanceOf(ArrayCollection::class, $combination->getIcons());
        $this->assertInstanceOf(ArrayCollection::class, $combination->getTranslations());
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
        $combination = new Combination();

        $this->assertSame($combination, $combination->setId($id));
        $this->assertSame($id, $combination->getId());
    }

    /**
     * Tests the setting and getting the import time.
     * @covers ::getImportTime
     * @covers ::setImportTime
     */
    public function testSetAndGetImportTime(): void
    {
        $importTime = new DateTime('2038-01-19 03:14:07');
        $combination = new Combination();

        $this->assertSame($combination, $combination->setImportTime($importTime));
        $this->assertSame($importTime, $combination->getImportTime());
    }

    /**
     * Tests the setting and getting the last usage time.
     * @covers ::getLastUsageTime
     * @covers ::setLastUsageTime
     */
    public function testSetAndGetLastUsageTime(): void
    {
        $lastUsageTime = new DateTime('2038-01-19 03:14:07');
        $combination = new Combination();

        $this->assertSame($combination, $combination->setLastUsageTime($lastUsageTime));
        $this->assertSame($lastUsageTime, $combination->getLastUsageTime());
    }
}
