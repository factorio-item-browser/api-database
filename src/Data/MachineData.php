<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Data;

use BluePsyduck\Common\Data\DataContainer;

/**
 * The class representing partial machine data.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class MachineData implements DataInterface
{
    /**
     * The id of the machine.
     * @var int
     */
    protected $id = 0;

    /**
     * The name of the machine.
     * @var string
     */
    protected $name = '';

    /**
     * The order of the machine.
     * @var int
     */
    protected $order = 0;

    /**
     * Sets the id of the machine.
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the id of the machine.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the name of the machine.
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the machine.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the order of the machine.
     * @param int $order
     * @return $this
     */
    public function setOrder(int $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Returns the order of the machine.
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
               ->setOrder($data->getInteger('order'));
        return $result;
    }
}
