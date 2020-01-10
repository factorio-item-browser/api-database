<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity class of the Mod database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Mod
{
    /**
     * The internal id of the mod.
     * @var UuidInterface
     */
    protected $id;

    /**
     * The name of the mod.
     * @var string
     */
    protected $name = '';

    /**
     * The version of the mod.
     * @var string
     */
    protected $version = '';

    /**
     * The author of the mod.
     * @var string
     */
    protected $author = '';

    /**
     * The combinations this mod is the part of.
     * @var Collection<int,Combination>
     */
    protected $combinations;

    /**
     * Initializes the entity.
     */
    public function __construct()
    {
        $this->combinations = new ArrayCollection();
    }

    /**
     * Sets the internal id of the item.
     * @param UuidInterface $id
     * @return $this Implementing fluent interface.
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the internal id of the item.
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Sets the name of the mod.
     * @param string $name
     * @return $this Implementing fluent interface.
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the mod.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the version of the mod.
     * @param string $version
     * @return $this Implementing fluent interface.
     */
    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Returns the version of the mod.
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Sets the author of the mod.
     * @param string $author
     * @return $this Implementing fluent interface.
     */
    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Returns the author of the mod.
     * @return string
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Returns the combinations this mod is part of.
     * @return Collection<int,Combination>
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
