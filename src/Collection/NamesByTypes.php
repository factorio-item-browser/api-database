<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Collection;

/**
 * A collection holding entity names grouped by their types.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class NamesByTypes
{
    /**
     * The values of the collection.
     * @var array|string[][]
     */
    protected $values = [];

    /**
     * Adds a type and name pair to the collection.
     * @param string $type
     * @param string $name
     * @return $this
     */
    public function addName(string $type, string $name): self
    {
        $this->values[$type][] = $name;
        return $this;
    }

    /**
     * Sets the names of the type.
     * @param string $type
     * @param array|string[] $names
     * @return $this
     */
    public function setNames(string $type, array $names): self
    {
        if (count($names) > 0) {
            $this->values[$type] = $names;
        } else {
            unset($this->values[$type]);
        }
        return $this;
    }

    /**
     * Returns the names of the type.
     * @param string $type
     * @return array|string[]
     */
    public function getNames(string $type): array
    {
        return $this->values[$type] ?? [];
    }

    /**
     * Returns whether the type and name pair is part of the collection.
     * @param string $type
     * @param string $name
     * @return bool
     */
    public function hasName(string $type, string $name): bool
    {
        return in_array($name, $this->values[$type] ?? [], true);
    }

    /**
     * Returns whether the collection is actually empty.
     * @return bool
     */
    public function isEmpty(): bool
    {
        return count($this->values) === 0;
    }

    /**
     * Transforms the collection into a two-dimensional array.
     * @return array|string[][]
     */
    public function toArray(): array
    {
        return $this->values;
    }
}
