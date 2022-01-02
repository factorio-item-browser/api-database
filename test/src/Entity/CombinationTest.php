<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the Combination class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\Combination
 */
class CombinationTest extends TestCase
{
    private function createInstance(): Combination
    {
        return new Combination();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getMods());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getItems());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getMachines());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getRecipes());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getIcons());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getTranslations());
    }

    public function testSetAndGetId(): void
    {
        $id = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());
    }

    public function testSetAndGetImportTime(): void
    {
        $importTime = new DateTime('2038-01-19 03:14:07');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setImportTime($importTime));
        $this->assertSame($importTime, $instance->getImportTime());
    }

    public function testSetAndGetLastUsageTime(): void
    {
        $lastUsageTime = new DateTime('2038-01-19 03:14:07');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setLastUsageTime($lastUsageTime));
        $this->assertSame($lastUsageTime, $instance->getLastUsageTime());
    }

    public function testSetAndGetLastUpdateCheckTime(): void
    {
        $value = new DateTime('2038-01-19 03:14:07');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setLastUpdateCheckTime($value));
        $this->assertSame($value, $instance->getLastUpdateCheckTime());
    }

    public function testSetAndGetLastUpdateHash(): void
    {
        $value = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setLastUpdateHash($value));
        $this->assertSame($value, $instance->getLastUpdateHash());
    }
}
