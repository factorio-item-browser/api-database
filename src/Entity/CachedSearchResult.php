<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use DateTime;
use DateTimeInterface;
use Exception;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity of the cached search result database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CachedSearchResult
{
    /**
     * The id of the combination used for the search.
     * @var UuidInterface
     */
    protected $combinationId;

    /**
     * The locale used for the search.
     * @var string
     */
    protected $locale = '';

    /**
     * The hash of the search.
     * @var UuidInterface
     */
    protected $searchHash;

    /**
     * The raw query string of the search.
     * @var string
     */
    protected $searchQuery = '';

    /**
     * The result data of the search.
     * @var string
     */
    protected $resultData = '';

    /**
     * The time when the search result was last used.
     * @var DateTimeInterface
     */
    protected $lastSearchTime;

    /**
     * Initializes the entity.
     * @throws Exception
     */
    public function __construct()
    {
        $this->lastSearchTime = new DateTime();
    }

    /**
     * Sets the id of the combination used for the search.
     * @param UuidInterface $combinationId
     * @return $this
     */
    public function setCombinationId(UuidInterface $combinationId): self
    {
        $this->combinationId = $combinationId;
        return $this;
    }

    /**
     * Returns the id of the combination used for the search.
     * @return UuidInterface
     */
    public function getCombinationId(): UuidInterface
    {
        return $this->combinationId;
    }

    /**
     * Sets the locale used for the search.
     * @param string $locale
     * @return $this
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Returns the locale used for the search.
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Sets the hash of the search.
     * @param UuidInterface $searchHash
     * @return $this
     */
    public function setSearchHash(UuidInterface $searchHash): self
    {
        $this->searchHash = $searchHash;
        return $this;
    }

    /**
     * Returns the hash of the search.
     * @return UuidInterface
     */
    public function getSearchHash(): UuidInterface
    {
        return $this->searchHash;
    }

    /**
     * Sets the raw query string of the search.
     * @param string $searchQuery
     * @return $this
     */
    public function setSearchQuery(string $searchQuery): self
    {
        $this->searchQuery = $searchQuery;
        return $this;
    }

    /**
     * Returns the raw query string of the search.
     * @return string
     */
    public function getSearchQuery(): string
    {
        return $this->searchQuery;
    }

    /**
     * Sets the result data of the search.
     * @param string $resultData
     * @return $this
     */
    public function setResultData(string $resultData): self
    {
        $this->resultData = $resultData;
        return $this;
    }

    /**
     * Returns the result data of the search.
     * @return string
     */
    public function getResultData(): string
    {
        return $this->resultData;
    }

    /**
     * Sets the time when the search result was last used.
     * @param DateTimeInterface $lastSearchTime
     * @return $this
     */
    public function setLastSearchTime(DateTimeInterface $lastSearchTime): self
    {
        $this->lastSearchTime = $lastSearchTime;
        return $this;
    }

    /**
     * Returns the time when the search result was last used.
     * @return DateTimeInterface
     */
    public function getLastSearchTime(): DateTimeInterface
    {
        return $this->lastSearchTime;
    }
}
