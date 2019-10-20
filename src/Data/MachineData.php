<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Data;

use Ramsey\Uuid\UuidInterface;

/**
 * The class representing partial machine data.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class MachineData
{
    /**
     * The id of the machine.
     * @var UuidInterface
     */
    protected $id;

    /**
     * The name of the machine.
     * @var string
     */
    protected $name = '';

    /**
     * Sets the id of the machine.
     * @param UuidInterface $id
     * @return $this
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the id of the machine.
     * @return UuidInterface
     */
    public function getId(): UuidInterface
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
}
