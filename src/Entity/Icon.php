<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Attribute\IncludeInIdCalculation;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Helper\Validator;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity of the icon database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the icons of the entities.',
])]
class Icon implements EntityWithId
{
    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the icon.'])]
    private UuidInterface $id;

    #[Id]
    #[Column(type: CustomTypes::ENUM_ENTITY_TYPE, options: ['comment' => "The type of the icon."])]
    #[IncludeInIdCalculation]
    private string $type = '';

    #[Id]
    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => "The name of the icon.",
    ])]
    #[IncludeInIdCalculation]
    private string $name = '';

    #[ManyToOne(targetEntity: IconData::class)]
    #[JoinColumn(name: 'dataId', nullable: false)]
    #[IncludeInIdCalculation]
    private IconData $data;

    /** @var Collection<int, Combination> */
    #[ManyToMany(targetEntity: Combination::class, mappedBy: 'items')]
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

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setName(string $name): self
    {
        $this->name = Validator::validateString($name);
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setData(IconData $data): self
    {
        $this->data = $data;
        return $this;
    }

    public function getData(): IconData
    {
        return $this->data;
    }

    /**
     * @return Collection<int, Combination>
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
