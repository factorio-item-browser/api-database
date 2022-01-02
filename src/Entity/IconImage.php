<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity of the icon image database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the icon image data.',
])]
class IconImage implements EntityWithId
{
    #[Id]
    #[Column(type: UuidBinaryType::NAME, options: ['comment' => 'The internal id of the image.'])]
    private UuidInterface $id;

    /** @var string|resource */
    #[Column(type: Types::BLOB, options: ['comment' => 'The contents of the image.'])]
    private mixed $contents = '';

    #[Column(type: Types::SMALLINT, options: [
        'unsigned' => true,
        'comment' => 'The size of the image.',
    ])]
    private int $size = 0;

    /** @var Collection<int, Icon> */
    #[OneToMany(mappedBy: 'image', targetEntity: Icon::class)]
    private Collection $icons;

    public function __construct()
    {
        $this->icons = new ArrayCollection();
    }

    public function setId(UuidInterface $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setContents(string $contents): self
    {
        $this->contents = $contents;
        return $this;
    }

    public function getContents(): string
    {
        if (is_resource($this->contents)) {
            $this->contents = (string) stream_get_contents($this->contents);
        }
        return $this->contents;
    }

    public function setSize(int $size): self
    {
        $this->size = $size;
        return $this;
    }

    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @return Collection<int, Icon>
     */
    public function getIcons(): Collection
    {
        return $this->icons;
    }
}
