<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

/**
 * The entity of the icon database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Icon
{
    /**
     * The internal id of the icon.
     * @var int|null
     */
    protected $id;

    /**
     * The mod combination adding the icon.
     * @var ModCombination
     */
    protected $modCombination;

    /**
     * The file of the icon.
     * @var IconFile
     */
    protected $file;

    /**
     * The type of the icon's prototype.
     * @var string
     */
    protected $type = '';

    /**
     * The name of the icons's prototype.
     * @var string
     */
    protected $name = '';

    /**
     * Initializes the entity.
     * @param ModCombination $modCombination
     * @param IconFile $iconFile
     */
    public function __construct(ModCombination $modCombination, IconFile $iconFile)
    {
        $this->modCombination = $modCombination;
        $this->file = $iconFile;
    }

    /**
     * Sets the internal id of the icon.
     * @param int $id
     * @return $this Implementing fluent interface.
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the internal id of the icon.
     * @return int
     */
    public function getId(): int
    {
        return (int) $this->id;
    }

    /**
     * Sets the mod combination adding the icon.
     * @param ModCombination $modCombination
     * @return $this Implementing fluent interface.
     */
    public function setModCombination(ModCombination $modCombination): self
    {
        $this->modCombination = $modCombination;
        return $this;
    }

    /**
     * Returns the mod combination adding the icon.
     * @return ModCombination
     */
    public function getModCombination(): ModCombination
    {
        return $this->modCombination;
    }

    /**
     * Sets the file of the icon.
     * @param IconFile $file
     * @return $this Implementing fluent interface.
     */
    public function setFile(IconFile $file): self
    {
        $this->file = $file;
        return $this;
    }

    /**
     * Returns the file of the icon.
     * @return IconFile
     */
    public function getFile(): IconFile
    {
        return $this->file;
    }

    /**
     * Sets the type of the icon's prototype.
     * @param string $type
     * @return $this Implementing fluent interface.
     */
    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the type of the icon's prototype.
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the name of the icons's prototype.
     * @param string $name
     * @return $this Implementing fluent interface.
     */
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the icons's prototype.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
