<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use BluePsyduck\TestHelper\ReflectionTrait;
use DateTime;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\UuidInterface;
use ReflectionException;

/**
 * The PHPUnit test of the CachedSearchResult class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @coversDefaultClass \FactorioItemBrowser\Api\Database\Entity\CachedSearchResult
 */
class CachedSearchResultTest extends TestCase
{
    use ReflectionTrait;

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
     * Tests the setting and getting the combination id.
     * @covers ::getCombinationId
     * @covers ::setCombinationId
     */
    public function testSetAndGetCombinationId(): void
    {
        /* @var UuidInterface&MockObject $combinationId */
        $combinationId = $this->createMock(UuidInterface::class);
        $cachedSearchResult = new CachedSearchResult();

        $this->assertSame($cachedSearchResult, $cachedSearchResult->setCombinationId($combinationId));
        $this->assertSame($combinationId, $cachedSearchResult->getCombinationId());
    }

    /**
     * Tests the setting and getting the locale.
     * @covers ::getLocale
     * @covers ::setLocale
     */
    public function testSetAndGetLocale(): void
    {
        $locale = 'abc';
        $cachedSearchResult = new CachedSearchResult();

        $this->assertSame($cachedSearchResult, $cachedSearchResult->setLocale($locale));
        $this->assertSame($locale, $cachedSearchResult->getLocale());
    }

    /**
     * Tests the setting and getting the search hash.
     * @covers ::getSearchHash
     * @covers ::setSearchHash
     */
    public function testSetAndGetSearchHash(): void
    {
        /* @var UuidInterface&MockObject $searchHash */
        $searchHash = $this->createMock(UuidInterface::class);
        $cachedSearchResult = new CachedSearchResult();

        $this->assertSame($cachedSearchResult, $cachedSearchResult->setSearchHash($searchHash));
        $this->assertSame($searchHash, $cachedSearchResult->getSearchHash());
    }

    /**
     * Tests the setting and getting the search query.
     * @covers ::getSearchQuery
     * @covers ::setSearchQuery
     */
    public function testSetAndGetSearchQuery(): void
    {
        $searchQuery = 'abc';
        $cachedSearchResult = new CachedSearchResult();

        $this->assertSame($cachedSearchResult, $cachedSearchResult->setSearchQuery($searchQuery));
        $this->assertSame($searchQuery, $cachedSearchResult->getSearchQuery());
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
     * Tests getting the result data.
     * @throws ReflectionException
     * @covers ::getResultData
     */
    public function testGetResultDataAsResource(): void
    {
        $contents = 'abc';

        $stream = fopen('php://memory', 'r+');
        if ($stream === false) {
            $this->fail('unable to open memory stream.');
        }
        fwrite($stream, $contents);
        fseek($stream, 0);

        $image = new CachedSearchResult();

        $this->injectProperty($image, 'resultData', $stream);
        $this->assertSame($contents, $image->getResultData());
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
