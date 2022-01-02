<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Translation;
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

    public function testSetAndGetId(): void
    {
        $id = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setId($id));
        $this->assertSame($id, $instance->getId());
    }

    public function testSetAndGetLocale(): void
    {
        $locale = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setLocale($locale));
        $this->assertSame($locale, $instance->getLocale());
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

    public function testSetAndGetValue(): void
    {
        $value = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setValue($value));
        $this->assertSame($value, $instance->getValue());
    }

    public function testSetAndGetDescription(): void
    {
        $description = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setDescription($description));
        $this->assertSame($description, $instance->getDescription());
    }

    public function testSetAndGetIsDuplicatedByRecipe(): void
    {
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setIsDuplicatedByRecipe(true));
        $this->assertTrue($instance->getIsDuplicatedByRecipe());
    }

    public function testSetAndGetIsDuplicatedByMachine(): void
    {
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setIsDuplicatedByMachine(true));
        $this->assertTrue($instance->getIsDuplicatedByMachine());
    }
}
