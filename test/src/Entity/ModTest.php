<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Mod;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the Mod class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\Mod
 */
class ModTest extends TestCase
{
    private function createInstance(): Mod
    {
        return new Mod();
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

    public function testName(): void
    {
        $value = 'ab';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setName($value));
        $this->assertSame($value, $instance->getName());

        $this->assertSame(str_repeat('abcde', 51), $instance->setName(str_repeat('abcde', 256))->getName());
    }

    public function testVersion(): void
    {
        $value = '1.2.3';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setVersion($value));
        $this->assertSame($value, $instance->getVersion());

        $this->assertSame(str_repeat('ab', 8), $instance->setVersion(str_repeat('ab', 32))->getVersion());
    }

    public function testAuthor(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setAuthor($value));
        $this->assertSame($value, $instance->getAuthor());

        $this->assertSame(str_repeat('abcde', 51), $instance->setAuthor(str_repeat('abcde', 256))->getAuthor());
    }

    public function testIdCalculation(): void
    {
        $instance = $this->createInstance();
        $instance->setId(Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450'))
                 ->setName('abc')
                 ->setVersion('1.2.3')
                 ->setAuthor('def')
            ;

        $expectedId = '4e4706ad-431d-98f3-c249-7d163e731151';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
