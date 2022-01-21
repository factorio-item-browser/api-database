<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Category;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the Category class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\Category
 */
class CategoryTest extends TestCase
{
    public function test(): void
    {
        $id = Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450');
        $type = 'abc';
        $name = 'def';

        $instance = new Category();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getRecipes());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getMachines());

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());

        $this->assertSame($instance, $instance->setType($type));
        $this->assertSame($type, $instance->getType());

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());
    }

    public function testValidation(): void
    {
        $name = str_repeat('abcde', 256);
        $expectedName = str_repeat('abcde', 51);

        $instance = new Category();

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($expectedName, $instance->getName());
    }

    public function testIdCalculation(): void
    {
        $instance = new Category();
        $instance->setId(Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450'))
                 ->setType('abc')
                 ->setName('def');

        $expectedId = 'e2ddb49e-1a3c-4d25-0529-5ea1ab9acaa7';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
