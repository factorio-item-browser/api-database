<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Mod;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Mod class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\Mod
 */
class ModTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     * @covers ::getCombinations
     */
    public function testConstruct(): void
    {
        $mod = new Mod();

        $this->assertInstanceOf(ArrayCollection::class, $mod->getCombinations());
    }

    /**
     * Tests setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);
        $mod = new Mod();

        $this->assertSame($mod, $mod->setId($id));
        $this->assertSame($id, $mod->getId());
    }

    /**
     * Tests setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $mod = new Mod();

        $this->assertSame($mod, $mod->setName($name));
        $this->assertSame($name, $mod->getName());
    }

    /**
     * Tests setting and getting the Version.
     * @covers ::getVersion
     * @covers ::setVersion
     */
    public function testSetAndGetVersion(): void
    {
        $mod = new Mod();

        $version = '1.2.3';
        $this->assertSame($mod, $mod->setVersion($version));
        $this->assertSame($version, $mod->getVersion());
    }

    /**
     * Tests setting and getting the author.
     * @covers ::getAuthor
     * @covers ::setAuthor
     */
    public function testSetAndGetAuthor(): void
    {
        $author = 'abc';
        $mod = new Mod();

        $this->assertSame($mod, $mod->setAuthor($author));
        $this->assertSame($author, $mod->getAuthor());
    }
}
