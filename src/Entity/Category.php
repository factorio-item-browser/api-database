<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Attribute\IncludeInIdCalculation;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Helper\Validator;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity representing a category of a recipe or machine.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the categories of recipes and machines.',
])]
#[Index(columns: ['type', 'name'])]
class Category implements EntityWithId
{
    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the category.'])]
    private UuidInterface $id;

    #[Column(type: CustomTypes::ENUM_CATEGORY_TYPE, options: ['comment' => 'The type of the category.'])]
    #[IncludeInIdCalculation]
    private string $type;

    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The name of the category.',
    ])]
    #[IncludeInIdCalculation]
    private string $name;

    /** @var Collection<int, Machine> */
    #[ManyToMany(targetEntity: Machine::class, mappedBy: 'categories')]
    private Collection $machines;

    /** @var Collection<int, Recipe> */
    #[OneToMany(mappedBy: 'category', targetEntity: Recipe::class)]
    private Collection $recipes;

    public function __construct()
    {
        $this->machines = new ArrayCollection();
        $this->recipes = new ArrayCollection();
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

    /**
     * @return Collection<int, Machine>
     */
    public function getMachines(): Collection
    {
        return $this->machines;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }
}
