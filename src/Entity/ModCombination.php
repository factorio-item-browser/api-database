<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * The entity representing the ModCombination database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ModCombination
{
    /**
     * The id of the mod combination.
     * @var int|null
     */
    protected $id;

    /**
     * The main mod.
     * @var Mod
     */
    protected $mod;

    /**
     * The name of the mod combination.
     * @var string
     */
    protected $name = '';

    /**
     * The list of the loaded optional mods.
     * @var array|int[]
     */
    protected $optionalModIds = [];

    /**
     * The order of the mod combination.
     * @var int
     */
    protected $order = 0;

    /**
     * The items added by the mod combination.
     * @var Collection|Item[]
     */
    protected $items;

    /**
     * The recipes added by the mod combination.
     * @var Collection|Recipe[]
     */
    protected $recipes;

    /**
     * The machines added by the mod combination.
     * @var Collection|Machine[]
     */
    protected $machines;

    /**
     * The translations added by the mod combination.
     * @var Collection|Translation[]
     */
    protected $translations;

    /**
     * The icons used by the mod combination.
     * @var Collection|Icon[]
     */
    protected $icons;

    /**
     * Initializes the combination.
     * @param Mod $mod
     * @param string $name
     */
    public function __construct(Mod $mod, string $name)
    {
        $this->mod = $mod;
        $this->name = $name;

        $this->items = new ArrayCollection();
        $this->recipes = new ArrayCollection();
        $this->machines = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->icons = new ArrayCollection();
    }

    /**
     * Sets the id of the mod combination.
     * @param int $id
     * @return $this Implementing fluent interface.
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the id of the mod combination.
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * Sets the main mod.
     * @param Mod $mod
     * @return $this Implementing fluent interface.
     */
    public function setMod(Mod $mod): self
    {
        $this->mod = $mod;
        return $this;
    }

    /**
     * Returns the main mod.
     * @return Mod
     */
    public function getMod(): Mod
    {
        return $this->mod;
    }

    /**
     * Sets the name of the mod combination.
     * @param string $name
     * @return $this Implementing fluent interface.
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the mod combination.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the list of the loaded optional mods.
     * @param array|int[] $optionalModIds
     * @return $this Implementing fluent interface.
     */
    public function setOptionalModIds(array $optionalModIds): self
    {
        $this->optionalModIds = $optionalModIds;
        return $this;
    }

    /**
     * Returns the list of the loaded optional mods.
     * @return array|int[]
     */
    public function getOptionalModIds(): array
    {
        return $this->optionalModIds;
    }

    /**
     * Sets the order of the mod combination.
     * @param int $order
     * @return $this Implementing fluent interface.
     */
    public function setOrder(int $order): self
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Returns the order of the mod combination.
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * Returns the items added by the mod combination.
     * @return Collection|Item[]
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * Returns the recipes added by the mod combination.
     * @return Collection|Recipe[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    /**
     * Returns the machines added by the mod combination.
     * @return Collection|Machine[]
     */
    public function getMachines(): Collection
    {
        return $this->machines;
    }

    /**
     * Returns the translations added by the mod combination.
     * @return Collection|Translation[]
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    /**
     * Returns the icons used by the mod combination.
     * @return Collection|Icon[]
     */
    public function getIcons(): Collection
    {
        return $this->icons;
    }
}
