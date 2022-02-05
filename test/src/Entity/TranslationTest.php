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
    private function createInstance(): Translation
    {
        return new Translation();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(ArrayCollection::class, $instance->getCombinations());
    }

    public function testId(): void
    {
        $value = Uuid::fromString('01e704f7-e602-4f24-87b0-1b6c4928e450');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($value));
        $this->assertSame($value, $instance->getId());
    }

    public function testLocale(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setLocale($value));
        $this->assertSame($value, $instance->getLocale());

        $this->assertSame('ababa', $instance->setLocale(str_repeat('ab', 16))->getLocale());
    }

    public function testType(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setType($value));
        $this->assertSame($value, $instance->getType());
    }

    public function testName(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setName($value));
        $this->assertSame($value, $instance->getName());

        $this->assertSame(str_repeat('abcde', 51), $instance->setName(str_repeat('abcde', 256))->getName());
    }

    public function testLabel(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setLabel($value));
        $this->assertSame($value, $instance->getLabel());

        $this->assertSame(str_repeat('abcde', 13107), $instance->setLabel(str_repeat('abcde', 65535))->getLabel());
    }

    public function testDescription(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setDescription($value));
        $this->assertSame($value, $instance->getDescription());

        $this->assertSame(
            str_repeat('abcde', 13107),
            $instance->setDescription(str_repeat('abcde', 65535))->getDescription(),
        );
    }

    public function testIdCalculation(): void
    {
        $instance = $this->createInstance();
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
