<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity representing the Combination database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Combination
{
    /**
     * The internal id of the combination.
     * @var UuidInterface
     */
    protected $id;

    /**
     * The mods added by the combination.
     * @var Collection|Mod[]
     */
    protected $mods;
    
    /**
     * The items added by the combination.
     * @var Collection|Item[]
     */
    protected $items;

    /**
     * The recipes added by the combination.
     * @var Collection|Recipe[]
     */
    protected $recipes;

    /**
     * The machines added by the combination.
     * @var Collection|Machine[]
     */
    protected $machines;

    /**
     * The translations added by the combination.
     * @var Collection|Translation[]
     */
    protected $translations;

    /**
     * The icons used by the combination.
     * @var Collection|Icon[]
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
     * Returns the mods added by the combination.
     * @return Collection|Mod[]
     */
    public function getMods(): Collection
    {
        return $this->mods;
    }

    /**
     * Returns the items added by the combination.
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Returns the recipes added by the combination.
     * @return Collection|Recipe[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    /**
     * Returns the machines added by the combination.
     * @return Collection|Machine[]
     */
    public function getMachines(): Collection
    {
        return $this->machines;
    }

    /**
     * Returns the translations added by the combination.
     * @return Collection|Translation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    /**
     * Returns the icons used by the combination.
     * @return Collection|Icon[]
     */
    public function getIcons(): Collection
    {
        return $this->icons;
    }
}
