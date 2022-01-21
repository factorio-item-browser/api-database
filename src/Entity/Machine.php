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
use FactorioItemBrowser\Api\Database\Attribute\IncludeInIdCalculation;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Helper\Validator;
use FactorioItemBrowser\Common\Constant\EnergyUsageUnit;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity representing a crafting machine, miner or pump.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the crafting machines of recipes.',
])]
#[Index(columns: ['name'])]
class Machine implements EntityWithId
{
    private const FACTOR_SPEED = 1000;
    private const FACTOR_ENERGY_USAGE = 1000;
    public const VALUE_UNLIMITED_SLOTS = 255;

    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the machine.'])]
    private UuidInterface $id;

    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The name of the machine.',
    ])]
    #[IncludeInIdCalculation]
    private string $name;

    /** @var Collection<int, Category> */
    #[ManyToMany(targetEntity: Category::class)]
    #[JoinTable(name: 'MachineXCategory')]
    #[JoinColumn(name: 'machineId', nullable: false)]
    #[InverseJoinColumn(name: 'categoryId', nullable: false)]
    #[IncludeInIdCalculation]
    private Collection $categories;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The speed of the machine.',
    ])]
    #[IncludeInIdCalculation]
    private int $speed = 0;

    #[Column(type: CustomTypes::TINYINT, options: [
        'unsigned' => true,
        'comment' => 'The number of item slots available in the machine, or 255 for unlimited.',
    ])]
    #[IncludeInIdCalculation]
    private int $numberOfItemSlots = 0;

    #[Column(type: CustomTypes::TINYINT, options: [
        'unsigned' => true,
        'comment' => 'The number of fluid input slots available in the machine.',
    ])]
    #[IncludeInIdCalculation]
    private int $numberOfFluidInputSlots = 0;

    #[Column(type: CustomTypes::TINYINT, options: [
        'unsigned' => true,
        'comment' => 'The number of fluid output slots available in the machine.',
    ])]
    #[IncludeInIdCalculation]
    private int $numberOfFluidOutputSlots = 0;

    #[Column(type: CustomTypes::TINYINT, options: [
        'unsigned' => true,
        'comment' => 'The number of module slots available in the machine.',
    ])]
    #[IncludeInIdCalculation]
    private int $numberOfModuleSlots = 0;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The energy usage of the machine.',
    ])]
    #[IncludeInIdCalculation]
    private int $energyUsage = 0;

    #[Column(type: CustomTypes::ENUM_ENERGY_USAGE_UNIT_TYPE, options: ['comment' => 'The unit of the energy usage.'])]
    #[IncludeInIdCalculation]
    private string $energyUsageUnit = EnergyUsageUnit::WATT;

    /** @var Collection<int, Combination> */
    #[ManyToMany(targetEntity: Combination::class, mappedBy: 'machines')]
    private Collection $combinations;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
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
        $this->name = Validator::validateString($name);
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Collection<int, Category>
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    public function setSpeed(float $speed): self
    {
        $this->speed = Validator::validateInteger((int) ($speed * self::FACTOR_SPEED));
        return $this;
    }

    public function getSpeed(): float
    {
        return $this->speed / self::FACTOR_SPEED;
    }

    public function setNumberOfItemSlots(int $numberOfItemSlots): self
    {
        $this->numberOfItemSlots = Validator::validateTinyInteger($numberOfItemSlots);
        return $this;
    }

    public function getNumberOfItemSlots(): int
    {
        return $this->numberOfItemSlots;
    }

    public function setNumberOfFluidInputSlots(int $numberOfFluidInputSlots): self
    {
        $this->numberOfFluidInputSlots = Validator::validateTinyInteger($numberOfFluidInputSlots);
        return $this;
    }

    public function getNumberOfFluidInputSlots(): int
    {
        return $this->numberOfFluidInputSlots;
    }

    public function setNumberOfFluidOutputSlots(int $numberOfFluidOutputSlots): self
    {
        $this->numberOfFluidOutputSlots = Validator::validateTinyInteger($numberOfFluidOutputSlots);
        return $this;
    }

    public function getNumberOfFluidOutputSlots(): int
    {
        return $this->numberOfFluidOutputSlots;
    }

    public function setNumberOfModuleSlots(int $numberOfModuleSlots): self
    {
        $this->numberOfModuleSlots = Validator::validateTinyInteger($numberOfModuleSlots);
        return $this;
    }

    public function getNumberOfModuleSlots(): int
    {
        return $this->numberOfModuleSlots;
    }

    public function setEnergyUsage(float $energyUsage): self
    {
        $this->energyUsage = Validator::validateInteger((int) ($energyUsage * self::FACTOR_ENERGY_USAGE));
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
