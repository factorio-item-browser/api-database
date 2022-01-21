<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity representing the actual icon data.
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
class IconData implements EntityWithId
{
    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the icon data.'])]
    private UuidInterface $id;

    /** @var string|resource */
    #[Column(type: Types::BLOB, options: ['comment' => 'The contents of the image.'])]
    private mixed $contents = '';

    #[Column(type: Types::SMALLINT, options: [
        'unsigned' => true,
        'comment' => 'The size of the image.',
    ])]
    private int $size = 0;

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
}
