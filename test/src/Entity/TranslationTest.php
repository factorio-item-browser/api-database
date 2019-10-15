<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use FactorioItemBrowser\Api\Database\Entity\Mod;
use FactorioItemBrowser\Api\Database\Entity\Combination;
use FactorioItemBrowser\Api\Database\Entity\Translation;
use PHPUnit\Framework\TestCase;

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
     */
    public function testConstruct(): void
    {
        $modCombination = new Combination(new Mod('abc'), 'def');
        $translation = new Translation($modCombination, 'ghi', 'jkl', 'mno');

        $this->assertSame(0, $translation->getId());
        $this->assertSame($modCombination, $translation->getModCombination());
        $this->assertSame('ghi', $translation->getLocale());
        $this->assertSame('jkl', $translation->getType());
        $this->assertSame('mno', $translation->getName());
        $this->assertSame('', $translation->getValue());
        $this->assertSame('', $translation->getDescription());
        $this->assertFalse($translation->getIsDuplicatedByRecipe());
        $this->assertFalse($translation->getIsDuplicatedByMachine());
    }

    /**
     * Tests setting and getting the id.
     * @covers ::getId
     * @covers ::setId
     */
    public function testSetAndGetId(): void
    {
        $translation = new Translation(new Combination(new Mod('abc'), 'def'), 'ghi', 'jkl', 'mno');

        $id = 42;
        $this->assertSame($translation, $translation->setId($id));
        $this->assertSame($id, $translation->getId());
    }

    /**
     * Tests setting and getting the modCombination.
     * @covers ::getModCombination
     * @covers ::setModCombination
     */
    public function testSetAndGetModCombination(): void
    {
        $translation = new Translation(new Combination(new Mod('abc'), 'def'), 'ghi', 'jkl', 'mno');

        $modCombination = new Combination(new Mod('foo'), 'bar');
        $this->assertSame($translation, $translation->setModCombination($modCombination));
        $this->assertSame($modCombination, $translation->getModCombination());
    }

    /**
     * Tests setting and getting the locale.
     * @covers ::getLocale
     * @covers ::setLocale
     */
    public function testSetAndGetLocale(): void
    {
        $translation = new Translation(new Combination(new Mod('abc'), 'def'), 'ghi', 'jkl', 'mno');

        $locale = 'pqr';
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
        $translation = new Translation(new Combination(new Mod('abc'), 'def'), 'ghi', 'jkl', 'mno');

        $type = 'pqr';
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
        $translation = new Translation(new Combination(new Mod('abc'), 'def'), 'ghi', 'jkl', 'mno');

        $name = 'pqr';
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
        $translation = new Translation(new Combination(new Mod('abc'), 'def'), 'ghi', 'jkl', 'mno');

        $value = 'pqr';
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
        $translation = new Translation(new Combination(new Mod('abc'), 'def'), 'ghi', 'jkl', 'mno');

        $description = 'pqr';
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
        $translation = new Translation(new Combination(new Mod('abc'), 'def'), 'ghi', 'jkl', 'mno');

        $isDuplicatedByRecipe = true;
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
        $translation = new Translation(new Combination(new Mod('abc'), 'def'), 'ghi', 'jkl', 'mno');

        $isDuplicatedByMachine = true;
        $this->assertSame($translation, $translation->setIsDuplicatedByMachine($isDuplicatedByMachine));
        $this->assertTrue($translation->getIsDuplicatedByMachine());
    }
}
