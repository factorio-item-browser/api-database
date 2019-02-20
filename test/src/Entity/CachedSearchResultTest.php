<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use DateTime;
use Exception;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use PHPUnit\Framework\TestCase;

/**
 * The PHPUnit test of the CachedSearchResult class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\CachedSearchResult
 */
class CachedSearchResultTest extends TestCase
{
    /**
     * Tests the constructing.
     * @throws Exception
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $cachedSearchResult = new CachedSearchResult('12ab34cd');

        $this->assertSame('12ab34cd', $cachedSearchResult->getHash());
        $this->assertSame('', $cachedSearchResult->getResultData());
        $cachedSearchResult->getLastSearchTime(); // assertion through type hinting
    }

    /**
     * Tests setting and getting the hash.
     * @throws Exception
     * @covers ::getHash
     * @covers ::setHash
     */
    public function testSetAndGetHash(): void
    {
        $cachedSearchResult = new CachedSearchResult('ab12cd34');

        $hash = '12ab34cd';
        $this->assertSame($cachedSearchResult, $cachedSearchResult->setHash($hash));
        $this->assertSame($hash, $cachedSearchResult->getHash());
    }

    /**
     * Tests setting and getting the resultData.
     * @throws Exception
     * @covers ::getResultData
     * @covers ::setResultData
     */
    public function testSetAndGetResultData(): void
    {
        $cachedSearchResult = new CachedSearchResult('ab12cd34');

        $resultData = 'abc';
        $this->assertSame($cachedSearchResult, $cachedSearchResult->setResultData($resultData));
        $this->assertSame($resultData, $cachedSearchResult->getResultData());
    }

    /**
     * Tests setting and getting the lastSearchTime.
     * @throws Exception
     * @covers ::getLastSearchTime
     * @covers ::setLastSearchTime
     */
    public function testSetAndGetLastSearchTime(): void
    {
        $cachedSearchResult = new CachedSearchResult('ab12cd34');

        $lastSearchTime = new DateTime('2038-01-19 03:14:07');
        $this->assertSame($cachedSearchResult, $cachedSearchResult->setLastSearchTime($lastSearchTime));
        $this->assertSame($lastSearchTime, $cachedSearchResult->getLastSearchTime());
    }
}
