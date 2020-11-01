<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FactorioItemBrowser\Api\Database\Entity\Translation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

/**
 * The PHPUnit test of the Translation class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\Translation
 */
class TranslationTest extends TestCase
{
    /**
     * Tests the constructing.
     * @covers ::__construct
     * @covers ::getCombinations
     */
    public function testConstruct(): void
    {
        $translation = new Translation();

        $this->assertInstanceOf(ArrayCollection::class, $translation->getCombinations());
    }

    /**
     * Tests the setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        /* @var UuidInterface&MockObject $id */
        $id = $this->createMock(UuidInterface::class);
        $translation = new Translation();

        $this->assertSame($translation, $translation->setId($id));
        $this->assertSame($id, $translation->getId());
    }

    /**
     * Tests setting and getting the locale.
     * @covers ::getLocale
     * @covers ::setLocale
     */
    public function testSetAndGetLocale(): void
    {
        $locale = 'abc';
        $translation = new Translation();

        $this->assertSame($translation, $translation->setLocale($locale));
        $this->assertSame($locale, $translation->getLocale());
    }

    /**
     * Tests setting and getting the type.
     * @covers ::getType
     * @covers ::setType
     */
    public function testSetAndGetType(): void
    {
        $type = 'abc';
        $translation = new Translation();

        $this->assertSame($translation, $translation->setType($type));
        $this->assertSame($type, $translation->getType());
    }

    /**
     * Tests setting and getting the name.
     * @covers ::getName
     * @covers ::setName
     */
    public function testSetAndGetName(): void
    {
        $name = 'abc';
        $translation = new Translation();

        $this->assertSame($translation, $translation->setName($name));
        $this->assertSame($name, $translation->getName());
    }

    /**
     * Tests setting and getting the value.
     * @covers ::getValue
     * @covers ::setValue
     */
    public function testSetAndGetValue(): void
    {
        $value = 'abc';
        $translation = new Translation();

        $this->assertSame($translation, $translation->setValue($value));
        $this->assertSame($value, $translation->getValue());
    }

    /**
     * Tests setting and getting the description.
     * @covers ::getDescription
     * @covers ::setDescription
     */
    public function testSetAndGetDescription(): void
    {
        $description = 'abc';
        $translation = new Translation();

        $this->assertSame($translation, $translation->setDescription($description));
        $this->assertSame($description, $translation->getDescription());
    }

    /**
     * Tests setting and getting the isDuplicatedByRecipe.
     * @covers ::getIsDuplicatedByRecipe
     * @covers ::setIsDuplicatedByRecipe
     */
    public function testSetAndGetIsDuplicatedByRecipe(): void
    {
        $isDuplicatedByRecipe = true;
        $translation = new Translation();

        $this->assertSame($translation, $translation->setIsDuplicatedByRecipe($isDuplicatedByRecipe));
        $this->assertTrue($translation->getIsDuplicatedByRecipe());
    }

    /**
     * Tests setting and getting the isDuplicatedByMachine.
     * @covers ::getIsDuplicatedByMachine
     * @covers ::setIsDuplicatedByMachine
     */
    public function testSetAndGetIsDuplicatedByMachine(): void
    {
        $isDuplicatedByMachine = true;
        $translation = new Translation();

        $this->assertSame($translation, $translation->setIsDuplicatedByMachine($isDuplicatedByMachine));
        $this->assertTrue($translation->getIsDuplicatedByMachine());
    }
}
