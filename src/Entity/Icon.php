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
     * The combination adding the icon.
     * @var Combination
     */
    protected $combination;

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
     * The file of the icon.
     * @var IconFile
     */
    protected $file;

    /**
     * Sets the combination adding the icon.
     * @param Combination $combination
     * @return $this Implementing fluent interface.
     */
    public function setCombination(Combination $combination): self
    {
        $this->combination = $combination;
        return $this;
    }

    /**
     * Returns the combination adding the icon.
     * @return Combination
     */
    public function getCombination(): Combination
    {
        return $this->combination;
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
}
