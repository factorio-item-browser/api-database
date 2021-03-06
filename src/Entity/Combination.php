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
    protected UuidInterface $id;

    /**
     * The time when the combination was imported.
     * @var DateTimeInterface
     */
    protected DateTimeInterface $importTime;

    /**
     * The time when the combination was last used by a visitor.
     * @var DateTimeInterface
     */
    protected DateTimeInterface $lastUsageTime;

    /**
     * The last time this combination was checked for an update.
     * @var DateTimeInterface|null
     */
    protected ?DateTimeInterface $lastUpdateCheckTime = null;

    /**
     * The hash representing the mod versions used when the combination was last updated.
     * @var UuidInterface|null
     */
    protected ?UuidInterface $lastUpdateHash = null;

    /**
     * The mods added by the combination.
     * @var Collection<int, Mod>
     */
    protected Collection $mods;

    /**
     * The items added by the combination.
     * @var Collection<int, Item>
     */
    protected Collection $items;

    /**
     * The recipes added by the combination.
     * @var Collection<int, Recipe>
     */
    protected Collection $recipes;

    /**
     * The machines added by the combination.
     * @var Collection<int, Machine>
     */
    protected Collection $machines;

    /**
     * The translations added by the combination.
     * @var Collection<int, Translation>
     */
    protected Collection $translations;

    /**
     * The icons used by the combination.
     * @var Collection<int, Icon>
     */
    protected Collection $icons;

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
     * Sets the last time this combination was checked for an update.
     * @param DateTimeInterface|null $lastUpdateCheckTime
     * @return $this
     */
    public function setLastUpdateCheckTime(?DateTimeInterface $lastUpdateCheckTime): self
    {
        $this->lastUpdateCheckTime = $lastUpdateCheckTime;
        return $this;
    }

    /**
     * Returns the last time this combination was checked for an update.
     * @return DateTimeInterface|null
     */
    public function getLastUpdateCheckTime(): ?DateTimeInterface
    {
        return $this->lastUpdateCheckTime;
    }

    /**
     * Sets the hash representing the mod versions used when the combination was last updated.
     * @param UuidInterface|null $lastUpdateHash
     * @return $this
     */
    public function setLastUpdateHash(?UuidInterface $lastUpdateHash): self
    {
        $this->lastUpdateHash = $lastUpdateHash;
        return $this;
    }

    /**
     * Returns the hash representing the mod versions used when the combination was last updated.
     * @return UuidInterface|null
     */
    public function getLastUpdateHash(): ?UuidInterface
    {
        return $this->lastUpdateHash;
    }

    /**
     * Returns the mods added by the combination.
     * @return Collection<int, Mod>|Mod[]
     */
    public function getMods(): Collection
    {
        return $this->mods;
    }

    /**
     * Returns the items added by the combination.
     * @return Collection<int, Item>|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Returns the recipes added by the combination.
     * @return Collection<int, Recipe>|Recipe[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    /**
     * Returns the machines added by the combination.
     * @return Collection<int, Machine>|Machine[]
     */
    public function getMachines(): Collection
    {
        return $this->machines;
    }

    /**
     * Returns the translations added by the combination.
     * @return Collection<int, Translation>|Translation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    /**
     * Returns the icons used by the combination.
     * @return Collection<int, Icon>|Icon[]
     */
    public function getIcons(): Collection
    {
        return $this->icons;
    }
}
