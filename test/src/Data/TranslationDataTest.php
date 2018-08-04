<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Data;

use FactorioItemBrowser\Api\Database\Data\TranslationData;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the TranslationData class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Data\TranslationData
 */
class TranslationDataTest extends TestCase
{
    /**
     * Tests the constructing.
     * @coversNothing
     */
    public function testConstruct()
    {
        $data = new TranslationData();

        $this->assertSame('', $data->getLocale());
        $this->assertSame('', $data->getType());
        $this->assertSame('', $data->getName());
        $this->assertSame('', $data->getValue());
        $this->assertSame('', $data->getDescription());
        $this->assertFalse($data->getIsDuplicatedByRecipe());
        $this->assertFalse($data->getIsDuplicatedByMachine());
        $this->assertSame(0, $data->getOrder());
    }

    /**
     * Tests setting and getting the locale.
     * @covers ::getLocale
     * @covers ::setLocale
     */
    public function testSetAndGetLocale()
    {
        $data = new TranslationData();

        $locale = 'abc';
        $this->assertSame($data, $data->setLocale($locale));
        $this->assertSame($locale, $data->getLocale());
    }

    /**
     * Tests setting and getting the type.
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetType()
    {
        $data = new TranslationData();

        $type = 'abc';
        $this->assertSame($data, $data->setType($type));
        $this->assertSame($type, $data->getType());
    }

    /**
     * Tests setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName()
    {
        $data = new TranslationData();

        $name = 'abc';
        $this->assertSame($data, $data->setName($name));
        $this->assertSame($name, $data->getName());
    }

    /**
     * Tests setting and getting the value.
     * @covers ::getValue
     * @covers ::setValue
     */
    public function testSetAndGetValue()
    {
        $data = new TranslationData();

        $value = 'abc';
        $this->assertSame($data, $data->setValue($value));
        $this->assertSame($value, $data->getValue());
    }

    /**
     * Tests setting and getting the description.
     * @covers ::getDescription
     * @covers ::setDescription
     */
    public function testSetAndGetDescription()
    {
        $data = new TranslationData();

        $description = 'abc';
        $this->assertSame($data, $data->setDescription($description));
        $this->assertSame($description, $data->getDescription());
    }

    /**
     * Tests setting and getting the isDuplicatedByRecipe.
     * @covers ::getIsDuplicatedByRecipe
     * @covers ::setIsDuplicatedByRecipe
     */
    public function testSetAndGetIsDuplicatedByRecipe()
    {
        $data = new TranslationData();

        $this->assertSame($data, $data->setIsDuplicatedByRecipe(true));
        $this->assertTrue($data->getIsDuplicatedByRecipe());
    }

    /**
     * Tests setting and getting the isDuplicatedByMachine.
     * @covers ::getIsDuplicatedByMachine
     * @covers ::setIsDuplicatedByMachine
     */
    public function testSetAndGetIsDuplicatedByMachine()
    {
        $data = new TranslationData();

        $this->assertSame($data, $data->setIsDuplicatedByMachine(true));
        $this->assertTrue($data->getIsDuplicatedByMachine());
    }

    /**
     * Tests setting and getting the order.
     * @covers ::getOrder
     * @covers ::setOrder
     */
    public function testSetAndGetOrder()
    {
        $data = new TranslationData();

        $order = 42;
        $this->assertSame($data, $data->setOrder($order));
        $this->assertSame($order, $data->getOrder());
    }

    /**
     * Tests the getKeys method.
     * @covers ::getKeys
     */
    public function testGetKeys()
    {
        $data = new TranslationData();
        $data->setLocale('abc')
             ->setType('def')
             ->setName('ghi');
        $expectedResult = ['abc', 'def', 'ghi'];

        $result = $data->getKeys();
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Tests the createFromArray method.
     * @covers ::createFromArray
     */
    public function testCreateFromArray()
    {
        $array = [
            'locale' => 'abc',
            'type' => 'def',
            'name' => 'ghi',
            'value' => 'jkl',
            'description' => 'mno',
            'isDuplicatedByRecipe' => true,
            'isDuplicatedByMachine' => true,
            'order' => 42,
        ];

        $data = TranslationData::createFromArray($array);
        $this->assertSame('abc', $data->getLocale());
        $this->assertSame('def', $data->getType());
        $this->assertSame('ghi', $data->getName());
        $this->assertSame('jkl', $data->getValue());
        $this->assertSame('mno', $data->getDescription());
        $this->assertTrue($data->getIsDuplicatedByRecipe());
        $this->assertTrue($data->getIsDuplicatedByMachine());
        $this->assertSame(42, $data->getOrder());
    }
}
