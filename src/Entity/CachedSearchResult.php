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
     * The internal id of the search result.
     * @var UuidInterface
     */
    protected $id;

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
     * Sets the internal id of the search result.
     * @param UuidInterface $id
     * @return $this
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the internal id of the search result.
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
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
