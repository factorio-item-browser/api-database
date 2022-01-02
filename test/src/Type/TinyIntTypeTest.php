<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Type;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Type\TinyIntType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the TinyIntType class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Type\TinyIntType
 */
class TinyIntTypeTest extends TestCase
{
    /**
     * The instance to test against
     * @var Type
     */
    protected static $type;

    /**
     * Sets up the test case.
     * @throws Exception
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // We have to use the factory methods because Doctrine.
        Type::addType(CustomTypes::TINYINT, TinyIntType::class);
        self::$type = Type::getType(CustomTypes::TINYINT);
    }

    /**
     * Tests the getSQLDeclaration method.
     * @covers ::getSQLDeclaration
     */
    public function testGetSQLDeclaration(): void
    {
        $fieldDeclaration = ['abc' => 'def'];
        $integerDeclaration = 'foo';
        $expectedResult = 'TINYfoo';

        /* @var AbstractPlatform&MockObject $platform */
        $platform = $this->createMock(AbstractPlatform::class);
        $platform->expects($this->once())
                 ->method('getIntegerTypeDeclarationSQL')
                 ->with($this->identicalTo($fieldDeclaration))
                 ->willReturn($integerDeclaration);

        $result = self::$type->getSQLDeclaration($fieldDeclaration, $platform);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the getName method.
     * @covers ::getName
     */
    public function testGetName(): void
    {
        $expectedResult = 'tinyint';

        $result = self::$type->getName();

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the requiresSQLCommentHint method.
     * @covers ::requiresSQLCommentHint
     */
    public function testRequiresSQLCommentHint(): void
    {
        /* @var AbstractPlatform&MockObject $platform */
        $platform = $this->createMock(AbstractPlatform::class);

        $result = self::$type->requiresSQLCommentHint($platform);

        $this->assertTrue($result);
    }
}
