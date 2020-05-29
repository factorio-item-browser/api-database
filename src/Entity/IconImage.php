<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity of the icon image database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class IconImage
{
    /**
     * The internal id of the image.
     * @var UuidInterface
     */
    protected $id;

    /**
     * The contents of the image.
     * @var string|resource
     */
    protected $contents = '';

    /**
     * The size of the image.
     * @var int
     */
    protected $size = 0;

    /**
     * The icons using the image.
     * @var Collection<int,Icon>|Icon[]
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
     * Sets the internal id of the icon.
     * @param UuidInterface $id
     * @return $this Implementing fluent interface.
     */
    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Returns the internal id of the icon.
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * Sets the contents of the image.
     * @param string $contents
     * @return $this Implementing fluent interface.
     */
    public function setContents(string $contents): self
    {
        $this->contents = $contents;
        return $this;
    }

    /**
     * Returns the contents of the image.
     * @return string
     */
    public function getContents(): string
    {
        if (is_resource($this->contents)) {
            $this->contents = (string) stream_get_contents($this->contents);
        }
        return $this->contents;
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
     * Returns the icons using this image.
     * @return Collection<int,Icon>|Icon[]
     */
    public function getIcons(): Collection
    {
        return $this->icons;
    }
}
