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
     * @var array<string, array<string, boolean>>
     */
    private array $values = [];

    /**
     * Adds a type and name pair to the collection.
     */
    public function addName(string $type, string $name): self
    {
        if ($name === '') {
            unset($this->values[$type][$name]);
            if (count($this->values[$type] ?? []) === 0) {
                unset($this->values[$type]);
            }
        } else {
            $this->values[$type][$name] = true;
        }
        return $this;
    }

    /**
     * Sets the names of the type.
     * @param array<string> $names
     */
    public function setNames(string $type, array $names): self
    {
        if (count($names) === 0) {
            unset($this->values[$type]);
        } else {
            $this->values[$type] = [];
            foreach ($names as $name) {
                $this->values[$type][$name] = true;
            }
        }
        return $this;
    }

    /**
     * Returns the names of the type.
     * @return array<string>
     */
    public function getNames(string $type): array
    {
        return array_keys($this->values[$type] ?? []);
    }

    /**
     * Returns whether the type and name pair is part of the collection.
     */
    public function hasName(string $type, string $name): bool
    {
        return $this->values[$type][$name] ?? false;
    }

    /**
     * Clears all values from the collection.
     * @return $this
     */
    public function clear(): self
    {
        $this->values = [];
        return $this;
    }

    /**
     * Returns whether the collection is actually empty.
     */
    public function isEmpty(): bool
    {
        return count($this->values) === 0;
    }

    /**
     * Transforms the collection into a two-dimensional array.
     * @return array<string, array<string>>
     */
    public function toArray(): array
    {
        return array_map(fn($names) => array_keys($names), $this->values);
    }
}
