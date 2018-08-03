<?php

declare(strict_types=1);

namespace FactorioItemBrowserTestAsset\Api\Database\Data;

use FactorioItemBrowser\Api\Database\Data\DataInterface;

/**
 * A test implementation of the DataInterface.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TestData implements DataInterface
{
    /**
     * The order of the data.
     * @var int
     */
    protected $order;

    /**
     * The keys to identify identical data.
     * @var array|string[]
     */
    protected $keys;

    /**
     * Initializes the test data.
     * @param int $order
     * @param array|string[] $keys
     */
    public function __construct(int $order, array $keys)
    {
        $this->order = $order;
        $this->keys = $keys;
    }

    /**
     * Returns the order of the data.
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * Returns the keys to identify identical data.
     * @return array|string[]
     */
    public function getKeys(): array
    {
        return $this->keys;
    }
}
