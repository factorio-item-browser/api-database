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
 * @covers \FactorioItemBrowser\Api\Database\Entity\Combination
 */
class CombinationTest extends TestCase
{
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

    public function testSetAndGetId(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);
        $combination = new Combination();

        $this->assertSame($combination, $combination->setId($id));
        $this->assertSame($id, $combination->getId());
    }

    public function testSetAndGetImportTime(): void
    {
        $importTime = new DateTime('2038-01-19 03:14:07');
        $combination = new Combination();

        $this->assertSame($combination, $combination->setImportTime($importTime));
        $this->assertSame($importTime, $combination->getImportTime());
    }

    public function testSetAndGetLastUsageTime(): void
    {
        $lastUsageTime = new DateTime('2038-01-19 03:14:07');
        $combination = new Combination();

        $this->assertSame($combination, $combination->setLastUsageTime($lastUsageTime));
        $this->assertSame($lastUsageTime, $combination->getLastUsageTime());
    }

    public function testSetAndGetLastUpdateCheckTime(): void
    {
        $value = new DateTime('2038-01-19 03:14:07');
        $instance = new Combination();

        $this->assertSame($instance, $instance->setLastUpdateCheckTime($value));
        $this->assertSame($value, $instance->getLastUpdateCheckTime());
    }

    public function testSetAndGetLastUpdateHash(): void
    {
        $value = $this->createMock(UuidInterface::class);
        $instance = new Combination();

        $this->assertSame($instance, $instance->setLastUpdateHash($value));
        $this->assertSame($value, $instance->getLastUpdateHash());
    }
}
