<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity of the icon file database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class IconFile
{
    /**
     * The internal id of the icon file.
     * @var UuidInterface
     */
    protected $id;

    /**
     * The actual image data.
     * @var string|resource
     */
    protected $image = '';

    /**
     * The size of the image.
     * @var int
     */
    protected $size = 0;

    /**
     * The icons using the file.
     * @var Collection|Icon[]
     */
    protected $icons;

    /**
     * Initializes the entity.
     */
    public function __construct()
    {
        $this->icons = new ArrayCollection();
    }

    /**
     * Sets the hash of the icon.
     * @param UuidInterface $id
     * @return $this Implementing fluent interface.
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the hash of the icon.
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Sets the actual image data.
     * @param string $image
     * @return $this Implementing fluent interface.
     */
    public function setImage(string $image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * Returns the actual image data.
     * @return string
     */
    public function getImage(): string
    {
        if (is_resource($this->image)) {
            $this->image = (string) stream_get_contents($this->image);
        }
        return $this->image;
    }

    /**
     * Sets the size of the image.
     * @param int $size
     * @return $this
     */
    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Returns the size of the image.
     * @return int
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * Returns the icons using this file.
     * @return Collection|Icon[]
     */
    public function getIcons(): Collection
    {
        return $this->icons;
    }
}
