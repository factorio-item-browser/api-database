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
    private function createInstance(): Category
    {
        return new Category();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getRecipes());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getMachines());
    }

    public function testId(): void
    {
        $value = Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($value));
        $this->assertSame($value, $instance->getId());
    }

    public function testType(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setType($value));
        $this->assertSame($value, $instance->getType());
    }

    public function testName(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setName($value));
        $this->assertSame($value, $instance->getName());

        $this->assertSame(str_repeat('abcde', 51), $instance->setName(str_repeat('abcde', 256))->getName());
    }

    public function testIdCalculation(): void
    {
        $instance = $this->createInstance();
        $instance->setId(Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450'))
                 ->setType('abc')
                 ->setName('def');

        $expectedId = 'e2ddb49e-1a3c-4d25-0529-5ea1ab9acaa7';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
