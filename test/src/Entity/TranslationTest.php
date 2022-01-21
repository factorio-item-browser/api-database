<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Translation;
use FactorioItemBrowser\Api\Database\Helper\IdCalculator;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

/**
 * The PHPUnit test of the Translation class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\Translation
 */
class TranslationTest extends TestCase
{
    public function test(): void
    {
        $id = Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450');
        $locale = 'abc';
        $type = 'def';
        $name = 'ghi';
        $label = 'jkl';
        $description = 'mno';

        $instance = new Translation();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getCombinations());

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());

        $this->assertSame($instance, $instance->setLocale($locale));
        $this->assertSame($locale, $instance->getLocale());

        $this->assertSame($instance, $instance->setType($type));
        $this->assertSame($type, $instance->getType());

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($name, $instance->getName());

        $this->assertSame($instance, $instance->setLabel($label));
        $this->assertSame($label, $instance->getLabel());

        $this->assertSame($instance, $instance->setDescription($description));
        $this->assertSame($description, $instance->getDescription());
    }

    public function testValidation(): void
    {
        $locale = str_repeat('ab', 16);
        $expectedLocale = 'ababa';
        $name = str_repeat('abcde', 256);
        $expectedName = str_repeat('abcde', 51);
        $label = str_repeat('abcde', 65535);
        $expectedLabel = str_repeat('abcde', 13107);
        $description = str_repeat('abcde', 65535);
        $expectedDescription = str_repeat('abcde', 13107);

        $instance = new Translation();

        $this->assertSame($instance, $instance->setLocale($locale));
        $this->assertSame($expectedLocale, $instance->getLocale());

        $this->assertSame($instance, $instance->setName($name));
        $this->assertSame($expectedName, $instance->getName());

        $this->assertSame($instance, $instance->setLabel($label));
        $this->assertSame($expectedLabel, $instance->getLabel());

        $this->assertSame($instance, $instance->setDescription($description));
        $this->assertSame($expectedDescription, $instance->getDescription());
    }

    public function testIdCalculation(): void
    {
        $instance = new Translation();
        $instance->setId(Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450'))
                 ->setLocale('abc')
                 ->setType('def')
                 ->setName('ghi')
                 ->setLabel('jkl')
                 ->setDescription('mno');

        $expectedId = 'a0606de1-1ddc-a777-b78c-5b4b345c1c99';

        $idCalculator = new IdCalculator();
        $result = $idCalculator->calculateId($instance);

        $this->assertSame($expectedId, $result->toString());
    }
}
