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
    public function test(): void
    {
        $instance = new Combination();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getMods());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getItems());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getMachines());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getRecipes());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getIcons());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getTechnologies());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getTranslations());

        $id = Uuid::fromString('2f4a45fa-a509-a9d1-aae6-ffcf984a7a76');

        $lastUpdateHash = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $importTime = new DateTime('2038-01-17 03:14:07');
        $lastUsageTime = new DateTime('2038-01-18 03:14:07');
        $lastUpdateCheckTime = new DateTime('2038-01-19 03:14:07');
        $dataVersion = 12;
        $numberOfMods = 23;
        $numberOfItems = 34;
        $numberOfMachines = 45;
        $numberOfRecipes = 56;
        $numberOfTechnologies = 67;
        $numberOfTranslations = 78;
        $numberOfIcons = 89;

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());

        $this->assertSame($instance, $instance->setDataVersion($dataVersion));
        $this->assertSame($dataVersion, $instance->getDataVersion());

        $this->assertSame($instance, $instance->setImportTime($importTime));
        $this->assertSame($importTime, $instance->getImportTime());

        $this->assertSame($instance, $instance->setLastUsageTime($lastUsageTime));
        $this->assertSame($lastUsageTime, $instance->getLastUsageTime());

        $this->assertSame($instance, $instance->setLastUpdateCheckTime($lastUpdateCheckTime));
        $this->assertSame($lastUpdateCheckTime, $instance->getLastUpdateCheckTime());

        $this->assertSame($instance, $instance->setLastUpdateHash($lastUpdateHash));
        $this->assertSame($lastUpdateHash, $instance->getLastUpdateHash());

        $this->assertSame($instance, $instance->setNumberOfMods($numberOfMods));
        $this->assertSame($numberOfMods, $instance->getNumberOfMods());

        $this->assertSame($instance, $instance->setNumberOfItems($numberOfItems));
        $this->assertSame($numberOfItems, $instance->getNumberOfItems());

        $this->assertSame($instance, $instance->setNumberOfMachines($numberOfMachines));
        $this->assertSame($numberOfMachines, $instance->getNumberOfMachines());

        $this->assertSame($instance, $instance->setNumberOfRecipes($numberOfRecipes));
        $this->assertSame($numberOfRecipes, $instance->getNumberOfRecipes());

        $this->assertSame($instance, $instance->setNumberOfTechnologies($numberOfTechnologies));
        $this->assertSame($numberOfTechnologies, $instance->getNumberOfTechnologies());

        $this->assertSame($instance, $instance->setNumberOfTranslations($numberOfTranslations));
        $this->assertSame($numberOfTranslations, $instance->getNumberOfTranslations());

        $this->assertSame($instance, $instance->setNumberOfIcons($numberOfIcons));
        $this->assertSame($numberOfIcons, $instance->getNumberOfIcons());
    }
}
