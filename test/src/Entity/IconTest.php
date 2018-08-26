<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\Icon;
use FactorioItemBrowser\Api\Database\Entity\IconFile;
use FactorioItemBrowser\Api\Database\Entity\Mod;
use FactorioItemBrowser\Api\Database\Entity\ModCombination;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the Icon class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\Icon
 */
class IconTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $modCombination = new ModCombination(new Mod('abc'));
        $file = new IconFile('12ab34cd');

        $icon = new Icon($modCombination, $file);
        $this->assertSame(0, $icon->getId());
        $this->assertSame($modCombination, $icon->getModCombination());
        $this->assertSame($file, $icon->getFile());
        $this->assertSame('', $icon->getType());
        $this->assertSame('', $icon->getName());
    }

    /**
     * Tests setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        $icon = new Icon(new ModCombination(new Mod('foo')), new IconFile('ab12cd34'));

        $id = 42;
        $this->assertSame($icon, $icon->setId($id));
        $this->assertSame($id, $icon->getId());
    }

    /**
     * Tests setting and getting the modCombination.
     * @covers ::getModCombination
     * @covers ::setModCombination
     */
    public function testSetAndGetModCombination(): void
    {
        $icon = new Icon(new ModCombination(new Mod('foo')), new IconFile('ab12cd34'));

        $modCombination = new ModCombination(new Mod('abc'));
        $this->assertSame($icon, $icon->setModCombination($modCombination));
        $this->assertSame($modCombination, $icon->getModCombination());
    }

    /**
     * Tests setting and getting the file.
     * @covers ::getFile
     * @covers ::setFile
     */
    public function testSetAndGetFile(): void
    {
        $icon = new Icon(new ModCombination(new Mod('foo')), new IconFile('ab12cd34'));

        $file = new IconFile('12ab34cd');
        $this->assertSame($icon, $icon->setFile($file));
        $this->assertSame($file, $icon->getFile());
    }

    /**
     * Tests setting and getting the type.
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetType(): void
    {
        $icon = new Icon(new ModCombination(new Mod('foo')), new IconFile('ab12cd34'));

        $type = 'abc';
        $this->assertSame($icon, $icon->setType($type));
        $this->assertSame($type, $icon->getType());
    }

    /**
     * Tests setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $icon = new Icon(new ModCombination(new Mod('foo')), new IconFile('ab12cd34'));

        $name = 'abc';
        $this->assertSame($icon, $icon->setName($name));
        $this->assertSame($name, $icon->getName());
    }
}
