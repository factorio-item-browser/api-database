<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Type;

use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use FactorioItemBrowser\Api\Database\Type\EnumType;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the EnumType class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Type\EnumType
 */
class EnumTypeTest extends TestCase
{
    /**
     * The instance to test against
     * @var Type
     */
    protected static $type;

    /**
     * Sets up the test case.
     * @throws DBALException
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        // We have to use the factory methods because Doctrine.
        Type::addType(EnumType::NAME, EnumType::class);
        self::$type = Type::getType(EnumType::NAME);
    }

    /**
     * Tests the getSQLDeclaration method.
     * @covers ::getSQLDeclaration
     */
    public function testGetSQLDeclaration(): void
    {
        $fieldDeclaration = [
            'values' => 'abc,def, ghi',
        ];
        $expectedResult = 'ENUM("abc","def","ghi")';

        /* @var AbstractPlatform&MockObject $platform */
        $platform = $this->createMock(AbstractPlatform::class);
        $platform->expects($this->any())
                 ->method('quoteStringLiteral')
                 ->with($this->isType('string'))
                 ->willReturnCallback(function (string $value): string {
                     return sprintf('"%s"', $value);
                 });

        $result = self::$type->getSQLDeclaration($fieldDeclaration, $platform);

        $this->assertSame($expectedResult, $result);
    }

    /**
     * Tests the getSQLDeclaration method.
     * @covers ::getSQLDeclaration
     */
    public function testGetSQLDeclarationWithException(): void
    {
        $fieldDeclaration = [];

        /* @var AbstractPlatform&MockObject $platform */
        $platform = $this->createMock(AbstractPlatform::class);

        $this->expectException(DBALException::class);

        self::$type->getSQLDeclaration($fieldDeclaration, $platform);
    }

    /**
     * Tests the getName method.
     * @covers ::getName
     */
    public function testGetName(): void
    {
        $expectedResult = 'enum';

        $result = self::$type->getName();

        $this->assertSame($expectedResult, $result);
    }
}
