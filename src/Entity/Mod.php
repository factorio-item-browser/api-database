<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity class of the Mod database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(name: '`Mod`', options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the mods.',
])]
class Mod implements EntityWithId
{
    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the mod.'])]
    private UuidInterface $id;

    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The name of the mod.',
    ])]
    private string $name = '';

    #[Column(length: 16, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The version of the mod.',
    ])]
    private string $version = '';

    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'comment' => 'The author of the mod.',
    ])]
    private string $author = '';

    /** @var Collection<int, Combination> */
    #[ManyToMany(targetEntity: Combination::class, mappedBy: 'mods')]
    private Collection $combinations;

    public function __construct()
    {
        $this->combinations = new ArrayCollection();
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

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setAuthor(string $author): self
    {
        $this->author = $author;
        return $this;
    }

    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * @return Collection<int, Combination>
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
