<?php

declare(strict_types=1);

namespace FactorioItemBrowserTest\Api\Database\Entity;

use BluePsyduck\TestHelper\ReflectionTrait;
use DateTime;
use FactorioItemBrowser\Api\Database\Entity\CachedSearchResult;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use ReflectionException;

/**
 * The PHPUnit test of the CachedSearchResult class.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 * @covers \FactorioItemBrowser\Api\Database\Entity\CachedSearchResult
 */
class CachedSearchResultTest extends TestCase
{
    use ReflectionTrait;

    private function createInstance(): CachedSearchResult
    {
        return new CachedSearchResult();
    }

    public function testConstruct(): void
    {
        $instance = $this->createInstance();

        $this->assertInstanceOf(DateTime::class, $instance->getLastSearchTime());
    }

    public function testSetAndGetCombinationId(): void
    {
        $combinationId = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setCombinationId($combinationId));
        $this->assertSame($combinationId, $instance->getCombinationId());
    }

    public function testSetAndGetLocale(): void
    {
        $locale = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setLocale($locale));
        $this->assertSame($locale, $instance->getLocale());
    }

    public function testSetAndGetSearchHash(): void
    {
        $searchHash = Uuid::fromString('01234567-89ab-cdef-0123-456789abcdef');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setSearchHash($searchHash));
        $this->assertSame($searchHash, $instance->getSearchHash());
    }

    public function testSetAndGetSearchQuery(): void
    {
        $searchQuery = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setSearchQuery($searchQuery));
        $this->assertSame($searchQuery, $instance->getSearchQuery());
    }

    public function testSetAndGetResultData(): void
    {
        $resultData = 'abc';
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setResultData($resultData));
        $this->assertSame($resultData, $instance->getResultData());
    }

    /**
     * @throws ReflectionException
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

        $image = $this->createInstance();
        $this->injectProperty($image, 'resultData', $stream);

        $this->assertSame($contents, $image->getResultData());
    }

    public function testSetAndGetLastSearchTime(): void
    {
        $lastSearchTime = new DateTime('2038-01-19 03:14:07');
        $instance = $this->createInstance();

        $this->assertSame($instance, $instance->setLastSearchTime($lastSearchTime));
        $this->assertSame($lastSearchTime, $instance->getLastSearchTime());
    }
}
