<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\Recipe;
use FactorioItemBrowser\Api\Database\Entity\RecipeData;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the RecipeProduct class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\RecipeProduct
 */
class RecipeProductTest extends TestCase
{
    private function createInstance(): RecipeProduct
    {
        return new RecipeProduct();
    }

    public function testRecipeData(): void
    {
        $value = new RecipeData();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setRecipeData($value));
        $this->assertSame($value, $instance->getRecipeData());
    }

    public function testOrder(): void
    {
        $value = 42;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setOrder($value));
        $this->assertSame($value, $instance->getOrder());

        $this->assertSame(255, $instance->setOrder(1337)->getOrder());
    }

    public function testItem(): void
    {
        $value = new Item();
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setItem($value));
        $this->assertSame($value, $instance->getItem());
    }

    public function testAmountMin(): void
    {
        $value = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setAmountMin($value));
        $this->assertSame($value, $instance->getAmountMin());

        $this->assertSame(4294967.295, $instance->setAmountMin(123456789.123465)->getAmountMin());
    }

    public function testAmountMax(): void
    {
        $value = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setAmountMax($value));
        $this->assertSame($value, $instance->getAmountMax());

        $this->assertSame(4294967.295, $instance->setAmountMax(123456789.123465)->getAmountMax());
    }

    public function testProbability(): void
    {
        $value = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setProbability($value));
        $this->assertSame($value, $instance->getProbability());

        $this->assertSame(4294967.295, $instance->setProbability(123456789.123465)->getProbability());
    }

    public function testAmount(): void
    {
        $instance = $this->createInstance();
        $instance->setAmountMin(42)
                 ->setAmountMax(21)
                 ->setProbability(0.25);

        $this->assertSame(7.875, $instance->getAmount());
    }

    public function testIdCalculation(): void
    {
        $item = new Item();
        $item->setType('abc')
             ->setName('def');

        $instance = new RecipeProduct();
        $instance->setOrder(42)
                 ->setItem($item)
                 ->setAmountMin(13.37)
                 ->setAmountMax(73.31)
                 ->setProbability(0.21);

        $expectedId = '13dfacfe-67ee-a30d-9b9f-c009893448c7';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
