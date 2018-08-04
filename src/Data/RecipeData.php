<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Data;

use BluePsyduck\Common\Data\DataContainer;

/**
 * The class representing partial recipe data.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RecipeData implements DataInterface
{
    /**
     * The id of the recipe.
     * @var int
     */
    protected $id = 0;

    /**
     * The name of the recipe.
     * @var string
     */
    protected $name = '';

    /**
     * The mode of the recipe.
     * @var string
     */
    protected $mode = '';

    /**
     * The item id related to the recipe data.
     * @var int
     */
    protected $itemId = 0;

    /**
     * The order of the recipe.
     * @var int
     */
    protected $order = 0;

    /**
     * Sets the id of the recipe.
     * @param int $id
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the id of the recipe.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the name of the recipe.
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the recipe.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the mode of the recipe.
     * @param string $mode
     * @return $this
     */
    public function setMode(string $mode)
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Returns the mode of the recipe.
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Sets the item id related to the recipe data.
     * @param int $itemId
     * @return $this
     */
    public function setItemId(int $itemId)
    {
        $this->itemId = $itemId;
        return $this;
    }

    /**
     * Returns the item id related to the recipe data.
     * @return int
     */
    public function getItemId(): int
    {
        return $this->itemId;
    }

    /**
     * Sets the order of the recipe.
     * @param int $order
     * @return $this
     */
    public function setOrder(int $order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Returns the order of the recipe.
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
        return [
            $this->name,
            $this->mode,
            (string) $this->itemId,
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
        $result->setId($data->getInteger('id'))
               ->setName($data->getString('name'))
               ->setMode($data->getString('mode'))
               ->setItemId($data->getInteger('itemId'))
               ->setOrder($data->getInteger('order'));
        return $result;
    }
}
