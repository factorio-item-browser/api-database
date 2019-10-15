<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use DateTime;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;

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
     * @covers ::__construct
     */
    public function testConstruct(): void
    {
        $cachedSearchResult = new CachedSearchResult();

        $this->assertInstanceOf(DateTime::class, $cachedSearchResult->getLastSearchTime());
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
        $cachedSearchResult = new CachedSearchResult();

        $this->assertSame($cachedSearchResult, $cachedSearchResult->setId($id));
        $this->assertSame($id, $cachedSearchResult->getId());
    }

    /**
     * Tests the setting and getting the result data.
     * @covers ::getResultData
     * @covers ::setResultData
     */
    public function testSetAndGetResultData(): void
    {
        $resultData = 'abc';
        $cachedSearchResult = new CachedSearchResult();

        $this->assertSame($cachedSearchResult, $cachedSearchResult->setResultData($resultData));
        $this->assertSame($resultData, $cachedSearchResult->getResultData());
    }

    /**
     * Tests the setting and getting the last search time.
     * @covers ::getLastSearchTime
     * @covers ::setLastSearchTime
     */
    public function testSetAndGetLastSearchTime(): void
    {
        $lastSearchTime = new DateTime('2038-01-19 03:14:07');
        $cachedSearchResult = new CachedSearchResult();

        $this->assertSame($cachedSearchResult, $cachedSearchResult->setLastSearchTime($lastSearchTime));
        $this->assertSame($lastSearchTime, $cachedSearchResult->getLastSearchTime());
    }
}
