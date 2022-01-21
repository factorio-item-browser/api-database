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
    public function test(): void
    {
        $id = Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450');
        $name = 'abc';
        $version = '1.2.3';
        $author = 'def';

        $instance = new Mod();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getCombinations());

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());

        $this->assertSame($instance, $instance->setVersion($version));
        $this->assertSame($version, $instance->getVersion());

        $this->assertSame($instance, $instance->setAuthor($author));
        $this->assertSame($author, $instance->getAuthor());
    }


    public function testValidation(): void
    {
        $name = str_repeat('abcde', 256);
        $expectedName = str_repeat('abcde', 51);
        $version = str_repeat('ab', 32);
        $expectedVersion = str_repeat('ab', 8);
        $author = str_repeat('abcde', 256);
        $expectedAuthor = str_repeat('abcde', 51);

        $instance = new Mod();

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($expectedName, $instance->getName());

        $this->assertSame($instance, $instance->setVersion($version));
        $this->assertSame($expectedVersion, $instance->getVersion());

        $this->assertSame($instance, $instance->setAuthor($author));
        $this->assertSame($expectedAuthor, $instance->getAuthor());
    }

    public function testIdCalculation(): void
    {
        $instance = new Mod();
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
