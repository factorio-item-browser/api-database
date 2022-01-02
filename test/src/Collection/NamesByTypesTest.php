<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Collection;

use FactorioItemBrowser\Api\Database\Collection\NamesByTypes;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the NamesByTypes class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Collection\NamesByTypes
 */
class NamesByTypesTest extends TestCase
{
    private function createInstance(): NamesByTypes
    {
        return new NamesByTypes();
    }

    public function test(): void
    {
        $instance = $this->createInstance();

        $this->assertTrue($instance->isEmpty());
        $this->assertSame([], $instance->toArray());

        $this->assertSame($instance, $instance->setNames('abc', ['def', 'ghi']));
        $this->assertFalse($instance->isEmpty());
        $this->assertSame(['abc' => ['def', 'ghi']], $instance->toArray());
        $this->assertSame(['def', 'ghi'], $instance->getNames('abc'));
        $this->assertTrue($instance->hasName('abc', 'def'));
        $this->assertFalse($instance->hasName('abc', 'jkl'));

        $this->assertSame($instance, $instance->addName('abc', 'jkl'));
        $this->assertSame(['abc' => ['def', 'ghi', 'jkl']], $instance->toArray());

        $instance->setNames('abc', []);
        $this->assertTrue($instance->isEmpty());
        $this->assertSame([], $instance->toArray());
    }
}
