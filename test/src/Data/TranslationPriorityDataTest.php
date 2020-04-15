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
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Data\TranslationPriorityData
 */
class TranslationPriorityDataTest extends TestCase
{
    /**
     * Tests the setting and getting the type.
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetType(): void
    {
        $type = 'abc';
        $data = new TranslationPriorityData();

        $this->assertSame($data, $data->setType($type));
        $this->assertSame($type, $data->getType());
    }

    /**
     * Tests the setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $data = new TranslationPriorityData();

        $this->assertSame($data, $data->setName($name));
        $this->assertSame($name, $data->getName());
    }

    /**
     * Tests the setting and getting the priority.
     * @covers ::getPriority
     * @covers ::setPriority
     */
    public function testSetAndGetPriority(): void
    {
        $priority = 42;
        $data = new TranslationPriorityData();

        $this->assertSame($data, $data->setPriority($priority));
        $this->assertSame($priority, $data->getPriority());
    }
}
