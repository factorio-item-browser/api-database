<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Category;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\Technology;
use FactorioItemBrowser\Api\Database\Entity\TechnologyData;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the Technology class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\Technology
 */
class TechnologyTest extends TestCase
{
    private function createInstance(): Technology
    {
        return new Technology();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getPrerequisites());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getRecipeUnlocks());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getCombinations());
    }

    public function testId(): void
    {
        $value = Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($value));
        $this->assertSame($value, $instance->getId());
    }

    public function testName(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setName($value));
        $this->assertSame($value, $instance->getName());

        $this->assertSame(str_repeat('abcde', 51), $instance->setName(str_repeat('abcde', 256))->getName());
    }

    public function testNormalData(): void
    {
        $value = new TechnologyData();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setNormalData($value));
        $this->assertSame($value, $instance->getNormalData());
    }

    public function testExpensiveData(): void
    {
        $value = new TechnologyData();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setExpensiveData($value));
        $this->assertSame($value, $instance->getExpensiveData());
    }

    public function testIdCalculation(): void
    {
        $recipe1 = new Recipe();
        $recipe1->setType('abc')
                ->setName('def')
                ->setCategory((new Category())->setName('foo'));
        $recipe2 = new Recipe();
        $recipe2->setType('ghi')
                ->setName('jkl')
                ->setCategory((new Category())->setName('bar'));

        $prerequisite1 = new Technology();
        $prerequisite1->setName('mno');
        $prerequisite2 = new Technology();
        $prerequisite2->setName('pqr');

        $normalData = new TechnologyData();
        $normalData->setCount(42);
        $expensiveData = new TechnologyData();
        $expensiveData->setCount(1337);

        $instance = $this->createInstance();
        $instance->setName('foo')
                 ->setNormalData($normalData)
                 ->setExpensiveData($expensiveData);
        $instance->getRecipeUnlocks()->add($recipe1);
        $instance->getRecipeUnlocks()->add($recipe2);
        $instance->getPrerequisites()->add($prerequisite1);
        $instance->getPrerequisites()->add($prerequisite2);

        $expectedId = 'b5387f5f-a030-2874-b221-3e7c6719d3a8';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
