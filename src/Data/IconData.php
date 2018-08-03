<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Data;

use BluePsyduck\Common\Data\DataContainer;

/**
 * The class representing partial icon data.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class IconData implements DataInterface
{
    /**
     * The id of the icon.
     * @var int
     */
    protected $id = 0;

    /**
     * The hash of the icon file.
     * @var string
     */
    protected $hash = '';

    /**
     * The type of the icon.
     * @var string
     */
    protected $type = '';

    /**
     * The name of the icon.
     * @var string
     */
    protected $name = '';

    /**
     * The order of the icon.
     * @var int
     */
    protected $order = 0;

    /**
     * Sets the id of the icon.
     * @param int $id
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the id of the icon.
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the hash of the icon file.
     * @param string $hash
     * @return $this
     */
    public function setHash(string $hash)
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Returns the hash of the icon file.
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }

    /**
     * Sets the type of the icon.
     * @param string $type
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the type of the icon.
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the name of the icon.
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the icon.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the order of the icon.
     * @param int $order
     * @return $this
     */
    public function setOrder(int $order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Returns the order of the icon.
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
        $result->setId($data->getInteger('id'))
               ->setHash(bin2hex($data->getString('hash')))
               ->setType($data->getString('type'))
               ->setName($data->getString('name'))
               ->setOrder($data->getInteger('order'));
        return $result;
    }
}
