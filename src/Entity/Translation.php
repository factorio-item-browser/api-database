<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity class of the Translation database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Translation
{
    /**
     * The internal id of the translation.
     * @var UuidInterface
     */
    protected $id;

    /**
     * The locale of the translation.
     * @var string
     */
    protected $locale = '';

    /**
     * The type of the translation.
     * @var string
     */
    protected $type = '';

    /**
     * The name of the translation.
     * @var string
     */
    protected $name = '';

    /**
     * The actual translation.
     * @var string
     */
    protected $value = '';

    /**
     * The translated description.
     * @var string
     */
    protected $description = '';

    /**
     * Whether this translation is duplicated by the recipe.
     * @var bool
     */
    protected $isDuplicatedByRecipe = false;

    /**
     * Whether this translation is duplicated by the machine.
     * @var bool
     */
    protected $isDuplicatedByMachine = false;

    /**
     * The combinations which are adding the translation.
     * @var Collection<int,Combination>|Combination[]
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
     * Sets the internal id of the translation.
     * @param UuidInterface $id
     * @return $this Implementing fluent interface.
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the internal id of the translation.
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Sets the locale of the translation.
     * @param string $locale
     * @return $this Implementing fluent interface.
     */
    public function setLocale(string $locale): self
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Returns the locale of the translation.
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Sets the type of the translation.
     * @param string $type
     * @return $this Implementing fluent interface.
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the type of the translation.
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the name of the translation.
     * @param string $name
     * @return $this Implementing fluent interface.
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the translation.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the value of the translation.
     * @param string $value
     * @return $this Implementing fluent interface.
     */
    public function setValue(string $value): self
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Returns the value of the translation.
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Sets the translated description.
     * @param string $description
     * @return $this Implementing fluent interface.
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Returns the translated description.
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets whether this translation is duplicated by the recipe.
     * @param bool $isDuplicatedByRecipe
     * @return $this Implementing fluent interface.
     */
    public function setIsDuplicatedByRecipe(bool $isDuplicatedByRecipe): self
    {
        $this->isDuplicatedByRecipe = $isDuplicatedByRecipe;
        return $this;
    }

    /**
     * Returns whether this translation is duplicated by the recipe.
     * @return bool
     */
    public function getIsDuplicatedByRecipe(): bool
    {
        return $this->isDuplicatedByRecipe;
    }

    /**
     * Sets whether this translation is duplicated by the machine.
     * @param bool $isDuplicatedByMachine
     * @return $this Implementing fluent interface.
     */
    public function setIsDuplicatedByMachine(bool $isDuplicatedByMachine): self
    {
        $this->isDuplicatedByMachine = $isDuplicatedByMachine;
        return $this;
    }

    /**
     * Returns whether this translation is duplicated by the machine.
     * @return bool
     */
    public function getIsDuplicatedByMachine(): bool
    {
        return $this->isDuplicatedByMachine;
    }

    /**
     * Returns the combinations which are adding the translation.
     * @return Collection<int,Combination>|Combination[]
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
