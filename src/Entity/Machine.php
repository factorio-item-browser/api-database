<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Type\EnumTypeEnergyUsageUnit;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity of the machine database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the crafting machines of the recipes.',
])]
#[Index(columns: ['name'])]
class Machine implements EntityWithId
{
    private const FACTOR_CRAFTING_SPEED = 1000;
    private const FACTOR_ENERGY_USAGE = 1000;

    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the machine.'])]
    private UuidInterface $id;

    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The name of the machine.',
    ])]
    private string $name;

    /** @var Collection<int, CraftingCategory> */
    #[ManyToMany(targetEntity: CraftingCategory::class)]
    #[JoinTable(name: 'MachineXCraftingCategory')]
    #[JoinColumn(name: 'machineId', nullable: false)]
    #[InverseJoinColumn(name: 'craftingCategoryId', nullable: false)]
    private Collection $craftingCategories;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The crafting speed of the machine.',
    ])]
    private int $craftingSpeed = self::FACTOR_CRAFTING_SPEED;

    #[Column(type: CustomTypes::TINYINT, options: [
        'unsigned' => true,
        'comment' => 'The number of item slots available in the machine, or 255 for unlimited.',
    ])]
    private int $numberOfItemSlots = 0;

    #[Column(type: CustomTypes::TINYINT, options: [
        'unsigned' => true,
        'comment' => 'The number of fluid input slots available in the machine.',
    ])]
    private int $numberOfFluidInputSlots = 0;

    #[Column(type: CustomTypes::TINYINT, options: [
        'unsigned' => true,
        'comment' => 'The number of fluid output slots available in the machine.',
    ])]
    private int $numberOfFluidOutputSlots = 0;

    #[Column(type: CustomTypes::TINYINT, options: [
        'unsigned' => true,
        'comment' => 'The number of module slots available in the machine.',
    ])]
    private int $numberOfModuleSlots = 0;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The energy usage of the machine.',
    ])]
    private int $energyUsage = 0;

    #[Column(type: EnumTypeEnergyUsageUnit::NAME, options: ['comment' => 'The unit of the energy usage.'])]
    private string $energyUsageUnit = '';

    /** @var Collection<int, Combination> */
    #[ManyToMany(targetEntity: Combination::class, mappedBy: 'machines')]
    private Collection $combinations;

    public function __construct()
    {
        $this->craftingCategories = new ArrayCollection();
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

    /**
     * @return Collection<int, CraftingCategory>
     */
    public function getCraftingCategories(): Collection
    {
        return $this->craftingCategories;
    }

    public function setCraftingSpeed(float $craftingSpeed): self
    {
        $this->craftingSpeed = (int) ($craftingSpeed * self::FACTOR_CRAFTING_SPEED);
        return $this;
    }

    public function getCraftingSpeed(): float
    {
        return $this->craftingSpeed / self::FACTOR_CRAFTING_SPEED;
    }

    public function setNumberOfItemSlots(int $numberOfItemSlots): self
    {
        $this->numberOfItemSlots = $numberOfItemSlots;
        return $this;
    }

    public function getNumberOfItemSlots(): int
    {
        return $this->numberOfItemSlots;
    }

    public function setNumberOfFluidInputSlots(int $numberOfFluidInputSlots): self
    {
        $this->numberOfFluidInputSlots = $numberOfFluidInputSlots;
        return $this;
    }

    public function getNumberOfFluidInputSlots(): int
    {
        return $this->numberOfFluidInputSlots;
    }

    public function setNumberOfFluidOutputSlots(int $numberOfFluidOutputSlots): self
    {
        $this->numberOfFluidOutputSlots = $numberOfFluidOutputSlots;
        return $this;
    }

    public function getNumberOfFluidOutputSlots(): int
    {
        return $this->numberOfFluidOutputSlots;
    }

    public function setNumberOfModuleSlots(int $numberOfModuleSlots): self
    {
        $this->numberOfModuleSlots = $numberOfModuleSlots;
        return $this;
    }

    public function getNumberOfModuleSlots(): int
    {
        return $this->numberOfModuleSlots;
    }

    public function setEnergyUsage(float $energyUsage): self
    {
        $this->energyUsage = (int) ($energyUsage * self::FACTOR_ENERGY_USAGE);
        return $this;
    }

    public function getEnergyUsage(): float
    {
        return $this->energyUsage / self::FACTOR_ENERGY_USAGE;
    }

    public function setEnergyUsageUnit(string $energyUsageUnit): self
    {
        $this->energyUsageUnit = $energyUsageUnit;
        return $this;
    }

    public function getEnergyUsageUnit(): string
    {
        return $this->energyUsageUnit;
    }

    /**
     * @return Collection<int, Combination>
     */
    public function getCombinations(): Collection
    {
        return $this->combinations;
    }
}
