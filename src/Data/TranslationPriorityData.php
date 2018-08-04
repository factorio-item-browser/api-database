<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Data;

use BluePsyduck\Common\Data\DataContainer;
use FactorioItemBrowser\Api\Database\Constant\SearchResultPriority;

/**
 * The class representing partial translation priority data.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TranslationPriorityData implements DataInterface
{
    /**
     * The type of the translation.
     * @var string
     */
    protected $type = '';

    /**
     * The name of the translation.
     * @var string
     */
    protected $name = '';

    /**
     * The priority of the translation.
     * @var int
     */
    protected $priority = SearchResultPriority::ANY_MATCH;

    /**
     * Sets the type of the translation.
     * @param string $type
     * @return $this
     */
    public function setType(string $type)
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
    public function setName(string $name)
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
    public function setPriority(int $priority)
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

    /**
     * Returns the order of the data.
     * @return int
     */
    public function getOrder(): int
    {
        return SearchResultPriority::ANY_MATCH - $this->priority;
    }

    /**
     * Returns the keys to identify identical data.
     * @return array|string[]
     */
    public function getKeys(): array
    {
        return [
            $this->type,
            $this->name,
        ];
    }

    /**
     * Creates a new instance from the specified data array.
     * @param array $dataArray
     * @return self
     */
    public static function createFromArray(array $dataArray): self
    {
        $data = new DataContainer($dataArray);

        $result = new self();
        $result->setType($data->getString('type'))
               ->setName($data->getString('name'))
               ->setPriority($data->getInteger('priority', SearchResultPriority::ANY_MATCH));
        return $result;
    }
}
