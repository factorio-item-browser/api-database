<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
     * @var int|null
     */
    protected $id;

    /**
     * The name of the mod.
     * @var string
     */
    protected $name = '';

    /**
     * The author of the mod.
     * @var string
     */
    protected $author = '';

    /**
     * The current version of the mod that has been imported.
     * @var string
     */
    protected $currentVersion = '';

    /**
     * The order position of the mod, 1 being the base mod.
     * @var int
     */
    protected $order = 0;

    /**
     * The dependencies of the mod.
     * @var Collection|ModDependency[]
     */
    protected $dependencies;

    /**
     * The combinations this mod is the main mod of.
     * @var Collection|ModCombination[]
     */
    protected $combinations;

    /**
     * Initializes the entity.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;

        $this->combinations = new ArrayCollection();
        $this->dependencies = new ArrayCollection();
    }

    /**
     * Sets the internal id of the item.
     * @param int $id
     * @return $this Implementing fluent interface.
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the internal id of the item.
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
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
     * Sets the current version of the mod that has been imported.
     * @param string $currentVersion
     * @return $this Implementing fluent interface.
     */
    public function setCurrentVersion(string $currentVersion): self
    {
        $this->currentVersion = $currentVersion;
        return $this;
    }

    /**
     * Returns the current version of the mod that has been imported.
     * @return string
     */
    public function getCurrentVersion(): string
    {
        return $this->currentVersion;
    }

    /**
     * Sets the order position of the mod, 1 being the base mod.
     * @param int $order
     * @return $this Implementing fluent interface.
     */
    public function setOrder(int $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Returns the order position of the mod, 1 being the base mod.
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * Returns the dependencies of the mod.
     * @return Collection|ModDependency[]
     */
    public function getDependencies(): Collection
    {
        return $this->dependencies;
    }

    /**
     * Returns the combinations this mod is the main mod of.
     * @return Collection|ModCombination[]
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
