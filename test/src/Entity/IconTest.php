<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Icon;
use FactorioItemBrowser\Api\Database\Entity\IconData;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the Icon class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\Icon
 */
class IconTest extends TestCase
{
    private function createInstance(): Icon
    {
        return new Icon();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getCombinations());
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
        $name = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());

        $this->assertSame(str_repeat('abcde', 51), $instance->setName(str_repeat('abcde', 256))->getName());
    }

    public function testData(): void
    {
        $value = new IconData();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setData($value));
        $this->assertSame($value, $instance->getData());
    }

    public function testIdCalculation(): void
    {
        $iconData = new IconData();
        $iconData->setId(Uuid::fromString('1964982c-6dee-4938-bcdc-6f84f0681d8f'));

        $instance = $this->createInstance();
        $instance->setId(Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450'))
                 ->setType('abc')
                 ->setName('def')
                 ->setData($iconData);

        $expectedId = '9e790f9d-88d3-1b1e-2ae3-bd6f6c2e4ee1';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
