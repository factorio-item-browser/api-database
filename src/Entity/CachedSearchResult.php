<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity of the cached search result database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table caching the search results.',
])]
class CachedSearchResult
{
    #[Id]
    #[Column(type: UuidBinaryType::NAME, options: ['comment' => 'The id of the combination.'])]
    private UuidInterface $combinationId;

    #[Id]
    #[Column(length: 5, options: ['comment' => 'The locale used for the search.'])]
    private string $locale = '';

    #[Id]
    #[Column(type: UuidBinaryType::NAME, options: ['comment' => 'The hash of the search.'])]
    private UuidInterface $searchHash;

    #[Column(type: Types::TEXT, length: 65535, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The raw query string of the search.',
    ])]
    private string $searchQuery = '';

    /**
     * @var string|resource
     */
    #[Column(type: Types::BLOB, options: ['comment' => 'The result data of the search.'])]
    private mixed $resultData = '';

    #[Column(type: CustomTypes::TIMESTAMP, options: ['comment' => 'The time when the search result was last used.'])]
    private DateTimeInterface $lastSearchTime;

    public function __construct()
    {
        $this->lastSearchTime = new DateTime();
    }

    public function setCombinationId(UuidInterface $combinationId): self
    {
        $this->combinationId = $combinationId;
        return $this;
    }

    public function getCombinationId(): UuidInterface
    {
        return $this->combinationId;
    }

    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function setSearchHash(UuidInterface $searchHash): self
    {
        $this->searchHash = $searchHash;
        return $this;
    }

    public function getSearchHash(): UuidInterface
    {
        return $this->searchHash;
    }

    public function setSearchQuery(string $searchQuery): self
    {
        $this->searchQuery = $searchQuery;
        return $this;
    }

    public function getSearchQuery(): string
    {
        return $this->searchQuery;
    }

    public function setResultData(string $resultData): self
    {
        $this->resultData = $resultData;
        return $this;
    }

    public function getResultData(): string
    {
        if (is_resource($this->resultData)) {
            $this->resultData = (string) stream_get_contents($this->resultData);
        }
        return $this->resultData;
    }

    public function setLastSearchTime(DateTimeInterface $lastSearchTime): self
    {
        $this->lastSearchTime = $lastSearchTime;
        return $this;
    }

    public function getLastSearchTime(): DateTimeInterface
    {
        return $this->lastSearchTime;
    }
}
