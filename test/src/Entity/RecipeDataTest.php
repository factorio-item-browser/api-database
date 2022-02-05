<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Item;
use FactorioItemBrowser\Api\Database\Entity\RecipeData;
use FactorioItemBrowser\Api\Database\Entity\RecipeIngredient;
use FactorioItemBrowser\Api\Database\Entity\RecipeProduct;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the RecipeData class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\RecipeData
 */
class RecipeDataTest extends TestCase
{
    private function createInstance(): RecipeData
    {
        return new RecipeData();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getIngredients());
        $this->assertInstanceOf(ArrayCollection::class, $instance->getProducts());
    }

    public function testId(): void
    {
        $value = Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($value));
        $this->assertSame($value, $instance->getId());
    }

    public function testTime(): void
    {
        $value = 13.37;
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setTime($value));
        $this->assertSame($value, $instance->getTime());

        $this->assertSame(4294967.295, $instance->setTime(123456789.123465)->getTime());
    }

    public function testIdCalculation(): void
    {
        $item1 = new Item();
        $item1->setType('abc')
              ->setName('def');
        $item2 = new Item();
        $item2->setType('ghi')
              ->setName('jkl');
        $item3 = new Item();
        $item3->setType('mno')
              ->setName('pqr');
        $item4 = new Item();
        $item4->setType('stu')
              ->setName('vwx');

        $ingredient1 = new RecipeIngredient();
        $ingredient1->setOrder(12)
                    ->setItem($item1);
        $ingredient2 = new RecipeIngredient();
        $ingredient2->setOrder(23)
                    ->setItem($item2);
        $product1 = new RecipeProduct();
        $product1->setOrder(34)
                 ->setItem($item3);
        $product2 = new RecipeProduct();
        $product2->setOrder(45)
                 ->setItem($item4);

        $instance = $this->createInstance();
        $instance->setTime(13.37);
        $instance->getIngredients()->add($ingredient1);
        $instance->getIngredients()->add($ingredient2);
        $instance->getProducts()->add($product1);
        $instance->getProducts()->add($product2);

        $expectedId = '880e498c-ec9e-653b-2bca-237b373274e7';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
