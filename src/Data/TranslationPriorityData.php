<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Data;

use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;

/**
 * The class representing partial translation priority data.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TranslationPriorityData
{
    /**
     * The type of the translation.
     */
    private string $type = '';

    /**
     * The name of the translation.
     */
    private string $name = '';

    /**
     * The priority of the translation.
     */
    private int $priority = SearchResultPriority::ANY_MATCH;

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }
}
