<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\Icon;
use FactorioItemBrowser\Api\Database\Entity\IconImage;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use PHPUnit\Framework\MockObject\MockObject;
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
     * Tests the setting and getting the combination.
     * @covers ::getCombination
     * @covers ::setCombination
     */
    public function testSetAndGetCombination(): void
    {
        /* @var Combination&MockObject $combination */
        $combination = $this->createMock(Combination::class);
        $icon = new Icon();

        $this->assertSame($icon, $icon->setCombination($combination));
        $this->assertSame($combination, $icon->getCombination());
    }

    /**
     * Tests the setting and getting the type.
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetType(): void
    {
        $type = 'abc';
        $icon = new Icon();

        $this->assertSame($icon, $icon->setType($type));
        $this->assertSame($type, $icon->getType());
    }

    /**
     * Tests the setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $icon = new Icon();

        $this->assertSame($icon, $icon->setName($name));
        $this->assertSame($name, $icon->getName());
    }

    /**
     * Tests the setting and getting the image.
     * @covers ::getImage
     * @covers ::setImage
     */
    public function testSetAndGetImage(): void
    {
        /* @var IconImage&MockObject $image */
        $image = $this->createMock(IconImage::class);
        $icon = new Icon();

        $this->assertSame($icon, $icon->setImage($image));
        $this->assertSame($image, $icon->getImage());
    }
}
