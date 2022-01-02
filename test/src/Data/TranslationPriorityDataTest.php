<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Data;

use FactorioItemBrowser\Api\Database\Data\TranslationPriorityData;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the TranslationPriorityData class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Data\TranslationPriorityData
 */
class TranslationPriorityDataTest extends TestCase
{
    private function createInstance(): TranslationPriorityData
    {
        return new TranslationPriorityData();
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

    public function testSetAndGetPriority(): void
    {
        $priority = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setPriority($priority));
        $this->assertSame($priority, $instance->getPriority());
    }
}
