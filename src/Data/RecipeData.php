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
     */
    private UuidInterface $id;

    /**
     * The name of the recipe.
     */
    private string $name = '';

    /**
     * The mode of the recipe.
     */
    private string $mode = '';

    /**
     * The item id related to the recipe data.
     */
    private ?UuidInterface $itemId;

    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setMode(string $mode): self
    {
        $this->mode = $mode;
        return $this;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function setItemId(?UuidInterface $itemId): self
    {
        $this->itemId = $itemId;
        return $this;
    }

    public function getItemId(): ?UuidInterface
    {
        return $this->itemId;
    }
}
