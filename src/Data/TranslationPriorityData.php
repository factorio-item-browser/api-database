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
     * @var string
     */
    protected string $type = '';

    /**
     * The name of the translation.
     * @var string
     */
    protected string $name = '';

    /**
     * The priority of the translation.
     * @var int
     */
    protected int $priority = SearchResultPriority::ANY_MATCH;

    /**
     * Sets the type of the translation.
     * @param string $type
     * @return $this
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the type of the translation.
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the name of the translation.
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the translation.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the priority of the translation.
     * @param int $priority
     * @return $this
     */
    public function setPriority(int $priority): self
    {
        $this->priority = $priority;
        return $this;
    }

    /**
     * Returns the priority of the translation.
     * @return int
     */
    public function getPriority(): int
    {
        return $this->priority;
    }
}
