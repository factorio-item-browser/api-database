<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity of the crafting category database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CraftingCategory implements EntityWithId
{
    /**
     * The internal id of the crafting category.
     * @var UuidInterface
     */
    protected UuidInterface $id;

    /**
     * The name of the crafting category.
     * @var string
     */
    protected string $name = '';

    /**
     * The machines supporting the crafting category.
     * @var Collection<int, Machine>
     */
    protected Collection $machines;

    /**
     * The recipes using the crafting category.
     * @var Collection<int, Recipe>
     */
    protected Collection $recipes;

    /**
     * Initializes the entity.
     */
    public function __construct()
    {
        $this->machines = new ArrayCollection();
        $this->recipes = new ArrayCollection();
    }

    /**
     * Sets the internal id of the crafting category.
     * @param UuidInterface $id
     * @return $this
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the internal id of the crafting category.
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
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
     * @return Collection<int, Machine>|Machine[]
     */
    public function getMachines(): Collection
    {
        return $this->machines;
    }

    /**
     * Returns the recipes using the crafting category.
     * @return Collection<int, Recipe>|Recipe[]
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }
}
