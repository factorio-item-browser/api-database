<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Mod;
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

    public function testSetAndGetId(): void
    {
        $id = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());
    }

    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());
    }

    public function testSetAndGetVersion(): void
    {
        $version = '1.2.3';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setVersion($version));
        $this->assertSame($version, $instance->getVersion());
    }

    public function testSetAndGetAuthor(): void
    {
        $author = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setAuthor($author));
        $this->assertSame($author, $instance->getAuthor());
    }
}
