<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Helper;

use FactorioItemBrowser\Api\Database\Data\DataInterface;

/**
 * A helper class for the data instances.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class DataHelper
{
    /**
     * Filters the data to only contain the items with the highest order.
     * @param array|DataInterface[] $data
     * @return array|DataInterface[]
     */
    public function filter(array $data): array
    {
        /* @var array|DataInterface[] $result */
        $result = [];
        foreach ($data as $item) {
            $key = implode('|', $item->getKeys());
            if (!isset($result[$key]) || $result[$key]->getOrder() < $item->getOrder()) {
                $result[$key] = $item;
            }
        }
        return array_values($result);
    }
}
