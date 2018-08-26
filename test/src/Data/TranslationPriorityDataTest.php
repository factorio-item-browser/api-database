<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Data;

use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;
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
     * Tests the constructing.
     * @coversNothing
     */
    public function testConstruct(): void
    {
        $data = new TranslationPriorityData();

        $this->assertSame('', $data->getType());
        $this->assertSame('', $data->getName());
        $this->assertSame(SearchResultPriority::ANY_MATCH, $data->getPriority());
    }

    /**
     * Tests setting and getting the type.
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetType(): void
    {
        $data = new TranslationPriorityData();

        $type = 'abc';
        $this->assertSame($data, $data->setType($type));
        $this->assertSame($type, $data->getType());
    }

    /**
     * Tests setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $data = new TranslationPriorityData();

        $name = 'abc';
        $this->assertSame($data, $data->setName($name));
        $this->assertSame($name, $data->getName());
    }

    /**
     * Tests setting and getting the priority.
     * @covers ::getPriority
     * @covers ::setPriority
     */
    public function testSetAndGetPriority(): void
    {
        $data = new TranslationPriorityData();

        $priority = 42;
        $this->assertSame($data, $data->setPriority($priority));
        $this->assertSame($priority, $data->getPriority());
    }

    /**
     * Provides the data for the getOrder test.
     * @return array
     */
    public function provideGetOrder(): array
    {
        return [
            [
                SearchResultPriority::ANY_MATCH, 0
            ],
            [
                SearchResultPriority::SECONDARY_LOCALE_MATCH,
                SearchResultPriority::ANY_MATCH - SearchResultPriority::SECONDARY_LOCALE_MATCH,
            ],
            [
                SearchResultPriority::PRIMARY_LOCALE_MATCH,
                SearchResultPriority::ANY_MATCH - SearchResultPriority::PRIMARY_LOCALE_MATCH,
            ],
            [
                SearchResultPriority::EXACT_MATCH,
                SearchResultPriority::ANY_MATCH - SearchResultPriority::EXACT_MATCH,
            ],
        ];
    }

    /**
     * Tests the getOrder method.
     * @param int $priority
     * @param int $expectedOrder
     * @covers ::getOrder
     * @dataProvider provideGetOrder
     */
    public function testGetOrder(int $priority, int $expectedOrder): void
    {
        $data = new TranslationPriorityData();
        $data->setPriority($priority);

        $result = $data->getOrder();
        $this->assertSame($expectedOrder, $result);
    }

    /**
     * Tests the getKeys method.
     * @covers ::getKeys
     */
    public function testGetKeys(): void
    {
        $data = new TranslationPriorityData();
        $data->setType('abc')
             ->setName('def');
        $expectedResult = ['abc', 'def'];

        $result = $data->getKeys();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the createFromArray method.
     * @covers ::createFromArray
     */
    public function testCreateFromArray(): void
    {
        $array = [
            'type' => 'abc',
            'name' => 'def',
            'priority' => 42,
        ];

        $data = TranslationPriorityData::createFromArray($array);
        $this->assertSame('abc', $data->getType());
        $this->assertSame('def', $data->getName());
        $this->assertSame(42, $data->getPriority());
    }
}
