<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity representing the Combination database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Combination implements EntityWithId
{
    /**
     * The internal id of the combination.
     * @var UuidInterface
     */
    protected $id;

    /**
     * The time when the combination was imported.
     * @var DateTimeInterface
     */
    protected $importTime;

    /**
     * The time when the combination was last used by a visitor.
     * @var DateTimeInterface
     */
    protected $lastUsageTime;

    /**
     * The mods added by the combination.
     * @var Collection<int,Mod>|Mod[]
     */
    protected $mods;
    
    /**
     * The items added by the combination.
     * @var Collection<int,Item>|Item[]
     */
    protected $items;

    /**
     * The recipes added by the combination.
     * @var Collection<int,Recipe>|Recipe[]
     */
    protected $recipes;

    /**
     * The machines added by the combination.
     * @var Collection<int,Machine>|Machine[]
     */
    protected $machines;

    /**
     * The translations added by the combination.
     * @var Collection<int,Translation>|Translation[]
     */
    protected $translations;

    /**
     * The icons used by the combination.
     * @var Collection<int,Icon>|Icon[]
     */
    protected $icons;

    /**
     * Initializes the combination.
     */
    public function __construct()
    {
        $this->mods = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->recipes = new ArrayCollection();
        $this->machines = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->icons = new ArrayCollection();
    }

    /**
     * Sets the internal id of the combination.
     * @param UuidInterface $id
     * @return $this Implementing fluent interface.
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the internal id of the combination.
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Sets the time when the combination was imported.
     * @param DateTimeInterface $importTime
     * @return $this
     */
    public function setImportTime(DateTimeInterface $importTime): self
    {
        $this->importTime = $importTime;
        return $this;
    }

    /**
     * Returns the time when the combination was imported.
     * @return DateTimeInterface
     */
    public function getImportTime(): DateTimeInterface
    {
        return $this->importTime;
    }

    /**
     * Sets the time when the combination was last used by a visitor.
     * @param DateTimeInterface $lastUsageTime
     * @return $this
     */
    public function setLastUsageTime(DateTimeInterface $lastUsageTime): self
    {
        $this->lastUsageTime = $lastUsageTime;
        return $this;
    }

    /**
     * Returns the time when the combination was last used by a visitor.
     * @return DateTimeInterface
     */
    public function getLastUsageTime(): DateTimeInterface
    {
        return $this->lastUsageTime;
    }

    /**
     * Returns the mods added by the combination.
     * @return Collection<int,Mod>|Mod[]
     */
    public function getMods(): Collection
    {
        return $this->mods;
    }

    /**
     * Returns the items added by the combination.
     * @return Collection<int,Item>|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Returns the recipes added by the combination.
     * @return Collection<int,Recipe>|Recipe[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    /**
     * Returns the machines added by the combination.
     * @return Collection<int,Machine>|Machine[]
     */
    public function getMachines(): Collection
    {
        return $this->machines;
    }

    /**
     * Returns the translations added by the combination.
     * @return Collection<int,Translation>|Translation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    /**
     * Returns the icons used by the combination.
     * @return Collection<int,Icon>|Icon[]
     */
    public function getIcons(): Collection
    {
        return $this->icons;
    }
}
