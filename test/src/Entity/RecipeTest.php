<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Category;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeData;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the Recipe class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\Recipe
 */
class RecipeTest extends TestCase
{
    private function createInstance(): Recipe
    {
        return new Recipe();
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

    public function testType(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setType($value));
        $this->assertSame($value, $instance->getType());
    }


    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());

        $this->assertSame(str_repeat('abcde', 51), $instance->setName(str_repeat('abcde', 256))->getName());
    }

    public function testCategory(): void
    {
        $value = new Category();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setCategory($value));
        $this->assertSame($value, $instance->getCategory());
    }

    public function testNormalData(): void
    {
        $value = new RecipeData();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setNormalData($value));
        $this->assertSame($value, $instance->getNormalData());
    }

    public function testExpensiveData(): void
    {
        $value = new RecipeData();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setExpensiveData($value));
        $this->assertSame($value, $instance->getExpensiveData());
    }

    public function testIdCalculation(): void
    {
        $category = new Category();
        $category->setType('abc')
                 ->setName('def');
        $normalData = new RecipeData();
        $normalData->setTime(4.2);
        $expensiveData = new RecipeData();
        $expensiveData->setTime(13.37);

        $instance = $this->createInstance();
        $instance->setType('ghi')
                 ->setName('jkl')
                 ->setCategory($category)
                 ->setNormalData($normalData)
                 ->setExpensiveData($expensiveData);

        $expectedId = '1e373a5c-503f-ac25-ca1a-d1c95a77c05a';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
