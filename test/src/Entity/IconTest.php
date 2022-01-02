<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\Icon;
use FactorioItemBrowser\Api\Database\Entity\IconImage;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use PHPUnit\Framework\TestCase;

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
    public function testSetAndGetCombination(): void
    {
        $combination = new Combination();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setCombination($combination));
        $this->assertSame($combination, $instance->getCombination());
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

    public function testSetAndGetImage(): void
    {
        $image = new IconImage();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setImage($image));
        $this->assertSame($image, $instance->getImage());
    }
}
