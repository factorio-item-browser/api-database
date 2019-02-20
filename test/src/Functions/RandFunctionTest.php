<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Functions;

use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\Query\SqlWalker;
use FactorioItemBrowser\Api\Database\Functions\RandFunction;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use ReflectionException;

/**
 * The PHPUnit test of the RandFunction class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Functions\RandFunction
 */
class RandFunctionTest extends TestCase
{
    /**
     * Tests the parse method.
     * @throws QueryException
     * @throws ReflectionException
     * @covers ::parse
     */
    public function testParse(): void
    {
        /* @var Parser|MockObject $parser */
        $parser = $this->getMockBuilder(Parser::class)
                       ->setMethods(['match'])
                       ->disableOriginalConstructor()
                       ->getMock();
        $parser->expects($this->exactly(3))
               ->method('match')
               ->withConsecutive(
                   [Lexer::T_IDENTIFIER],
                   [Lexer::T_OPEN_PARENTHESIS],
                   [Lexer::T_CLOSE_PARENTHESIS]
               )
               ->willReturnSelf();

        $function = new RandFunction('foo');
        $function->parse($parser);
    }

    /**
     * Tests the getSql method.
     * @throws ReflectionException
     * @covers ::getSql
     */
    public function testGetSql(): void
    {
        /* @var SqlWalker $sqlWalker */
        $sqlWalker = $this->createMock(SqlWalker::class);

        $function = new RandFunction('foo');
        $this->assertSame('RAND()', $function->getSql($sqlWalker));
    }
}
