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

/**
 * The PHPUnit test of the RandFunction class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Functions\RandFunction
 */
class RandFunctionTest extends TestCase
{
    /**
     * @throws QueryException
     */
    public function testParse(): void
    {
        $parser = $this->createMock(Parser::class);
        $parser->expects($this->exactly(3))
               ->method('match')
               ->withConsecutive(
                   [$this->identicalTo(Lexer::T_IDENTIFIER)],
                   [$this->identicalTo(Lexer::T_OPEN_PARENTHESIS)],
                   [$this->identicalTo(Lexer::T_CLOSE_PARENTHESIS)]
               )
               ->willReturnSelf();

        $function = new RandFunction('foo');
        $function->parse($parser);
    }

    public function testGetSql(): void
    {
        $sqlWalker = $this->createMock(SqlWalker::class);

        $function = new RandFunction('foo');
        $this->assertSame('RAND()', $function->getSql($sqlWalker));
    }
}
