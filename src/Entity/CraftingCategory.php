<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * The entity of the crafting category database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CraftingCategory
{
    /**
     * The internal id of the crafting category.
     * @var int|null
     */
    protected $id;

    /**
     * The name of the crafting category.
     * @var string
     */
    protected $name;

    /**
     * The machines supporting the crafting category.
     * @var Collection|ModCombination[]
     */
    protected $machines;

    /**
     * The recipes using the crafting category.
     * @var Collection|Recipe[]
     */
    protected $recipes;

    /**
     * Initializes the entity.
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
        $this->machines = new ArrayCollection();
        $this->recipes = new ArrayCollection();
    }

    /**
     * Sets the internal id of the crafting category.
     * @param int $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the internal id of the crafting category.
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * Sets the name of the crafting category.
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the crafting category.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the machines supporting the crafting category.
     * @return Collection|ModCombination[]
     */
    public function getMachines(): Collection
    {
        return $this->machines;
    }

    /**
     * Returns the recipes using the crafting category.
     * @return Collection|Recipe[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }
}
