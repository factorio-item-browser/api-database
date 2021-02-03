<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Data;

use Ramsey\Uuid\UuidInterface;

/**
 * The class representing partial recipe data.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class RecipeData
{
    /**
     * The id of the recipe.
     * @var UuidInterface
     */
    protected UuidInterface $id;

    /**
     * The name of the recipe.
     * @var string
     */
    protected string $name = '';

    /**
     * The mode of the recipe.
     * @var string
     */
    protected string $mode = '';

    /**
     * The item id related to the recipe data.
     * @var UuidInterface|null
     */
    protected ?UuidInterface $itemId;

    /**
     * Sets the id of the recipe.
     * @param UuidInterface $id
     * @return $this
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the id of the recipe.
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Sets the name of the recipe.
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the recipe.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the mode of the recipe.
     * @param string $mode
     * @return $this
     */
    public function setMode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * Returns the mode of the recipe.
     * @return string
     */
    public function getMode(): string
    {
        return $this->mode;
    }

    /**
     * Sets the item id related to the recipe data.
     * @param UuidInterface|null $itemId
     * @return $this
     */
    public function setItemId(?UuidInterface $itemId): self
    {
        $this->itemId = $itemId;
        return $this;
    }

    /**
     * Returns the item id related to the recipe data.
     * @return UuidInterface|null
     */
    public function getItemId(): ?UuidInterface
    {
        return $this->itemId;
    }
}
