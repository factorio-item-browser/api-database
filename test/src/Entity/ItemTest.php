<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Item;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Item class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\Item
 */
class ItemTest extends TestCase
{
    private function createInstance(): Item
    {
        return new Item();
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

    public function testSetAndGetType(): void
    {
        $type = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setType($type));
        $this->assertSame($type, $instance->getType());
    }

    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());
    }
}
