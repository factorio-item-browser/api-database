<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use DateTime;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Helper\Validator;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity representing a combination of mods.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the combinations of mods.',
])]
class Combination implements EntityWithId
{
    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the combination.'])]
    private UuidInterface $id;

    #[Column(type: CustomTypes::TINYINT, options: ['comment' => 'The version of data.'])]
    private int $dataVersion = 0;

    #[Column(type: CustomTypes::TIMESTAMP, options: ['comment' => 'The time when the combination was imported.'])]
    private DateTimeInterface $importTime;

    #[Column(type: CustomTypes::TIMESTAMP, options: [
        'comment' => 'The time when the combination was last used by a visitor.',
    ])]
    private DateTimeInterface $lastUsageTime;

    #[Column(type: CustomTypes::TIMESTAMP, nullable: true, options: [
        'comment' => 'The last time this combination was checked for an update.',
    ])]
    private ?DateTimeInterface $lastUpdateCheckTime = null;

    #[Column(type: CustomTypes::UUID, nullable: true, options: [
        'comment' => 'The hash representing the mod versions used when the combination was last updated.',
    ])]
    private ?UuidInterface $lastUpdateHash = null;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The number of mods in the combination.',
    ])]
    private int $numberOfMods = 0;

    /** @var Collection<int, Mod> */
    #[ManyToMany(targetEntity: Mod::class)]
    #[JoinTable(name: 'CombinationXMod')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'modId', nullable: false)]
    private Collection $mods;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The number of items in the combination.',
    ])]
    private int $numberOfItems = 0;

    /** @var Collection<int, Item> */
    #[ManyToMany(targetEntity: Item::class)]
    #[JoinTable(name: 'CombinationXItem')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'itemId', nullable: false)]
    private Collection $items;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The number of machines in the combination.',
    ])]
    private int $numberOfMachines = 0;

    /** @var Collection<int, Machine> */
    #[ManyToMany(targetEntity: Machine::class)]
    #[JoinTable(name: 'CombinationXMachine')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'machineId', nullable: false)]
    private Collection $machines;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The number of recipes in the combination.',
    ])]
    private int $numberOfRecipes = 0;

    /** @var Collection<int, Recipe> */
    #[ManyToMany(targetEntity: Recipe::class)]
    #[JoinTable(name: 'CombinationXRecipe')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'recipeId', nullable: false)]
    private Collection $recipes;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The number of technologies in the combination.',
    ])]
    private int $numberOfTechnologies = 0;

    /** @var Collection<int, Technology> */
    #[ManyToMany(targetEntity: Technology::class)]
    #[JoinTable(name: 'CombinationXTechnology')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'technologyId', nullable: false)]
    private Collection $technologies;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The number of translations in the combination.',
    ])]
    private int $numberOfTranslations = 0;

    /** @var Collection<int, Translation> */
    #[ManyToMany(targetEntity: Translation::class)]
    #[JoinTable(name: 'CombinationXTranslation')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'translationId', nullable: false)]
    private Collection $translations;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The number of icons in the combination.',
    ])]
    private int $numberOfIcons = 0;

    /** @var Collection<int, Icon> */
    #[ManyToMany(targetEntity: Icon::class)]
    #[JoinTable(name: 'CombinationXIcon')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'iconId', nullable: false)]
    private Collection $icons;

    public function __construct()
    {
        $this->mods = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->recipes = new ArrayCollection();
        $this->machines = new ArrayCollection();
        $this->technologies = new ArrayCollection();
        $this->translations = new ArrayCollection();
        $this->icons = new ArrayCollection();

        $this->lastUsageTime = new DateTime();
        $this->importTime = new DateTime();
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

    public function setDataVersion(int $dataVersion): self
    {
        $this->dataVersion = Validator::validateTinyInteger($dataVersion);
        return $this;
    }

    public function getDataVersion(): int
    {
        return $this->dataVersion;
    }

    public function setImportTime(DateTimeInterface $importTime): self
    {
        $this->importTime = $importTime;
        return $this;
    }

    public function getImportTime(): DateTimeInterface
    {
        return $this->importTime;
    }

    public function setLastUsageTime(DateTimeInterface $lastUsageTime): self
    {
        $this->lastUsageTime = $lastUsageTime;
        return $this;
    }

    public function getLastUsageTime(): DateTimeInterface
    {
        return $this->lastUsageTime;
    }

    public function setLastUpdateCheckTime(?DateTimeInterface $lastUpdateCheckTime): self
    {
        $this->lastUpdateCheckTime = $lastUpdateCheckTime;
        return $this;
    }

    public function getLastUpdateCheckTime(): ?DateTimeInterface
    {
        return $this->lastUpdateCheckTime;
    }

    public function setLastUpdateHash(?UuidInterface $lastUpdateHash): self
    {
        $this->lastUpdateHash = $lastUpdateHash;
        return $this;
    }

    public function getLastUpdateHash(): ?UuidInterface
    {
        return $this->lastUpdateHash;
    }

    public function setNumberOfMods(int $numberOfMods): self
    {
        $this->numberOfMods = Validator::validateInteger($numberOfMods);
        return $this;
    }

    public function getNumberOfMods(): int
    {
        return $this->numberOfMods;
    }

    /**
     * @return Collection<int, Mod>
     */
    public function getMods(): Collection
    {
        return $this->mods;
    }

    public function setNumberOfItems(int $numberOfItems): self
    {
        $this->numberOfItems = Validator::validateInteger($numberOfItems);
        return $this;
    }

    public function getNumberOfItems(): int
    {
        return $this->numberOfItems;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function setNumberOfMachines(int $numberOfMachines): self
    {
        $this->numberOfMachines = Validator::validateInteger($numberOfMachines);
        return $this;
    }

    public function getNumberOfMachines(): int
    {
        return $this->numberOfMachines;
    }

    /**
     * @return Collection<int, Machine>
     */
    public function getMachines(): Collection
    {
        return $this->machines;
    }

    public function setNumberOfRecipes(int $numberOfRecipes): self
    {
        $this->numberOfRecipes = Validator::validateInteger($numberOfRecipes);
        return $this;
    }

    public function getNumberOfRecipes(): int
    {
        return $this->numberOfRecipes;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function setNumberOfTechnologies(int $numberOfTechnologies): self
    {
        $this->numberOfTechnologies = Validator::validateInteger($numberOfTechnologies);
        return $this;
    }

    public function getNumberOfTechnologies(): int
    {
        return $this->numberOfTechnologies;
    }

    /**
     * @return Collection<int, Technology>
     */
    public function getTechnologies(): Collection
    {
        return $this->technologies;
    }

    public function setNumberOfTranslations(int $numberOfTranslations): self
    {
        $this->numberOfTranslations = Validator::validateInteger($numberOfTranslations);
        return $this;
    }

    public function getNumberOfTranslations(): int
    {
        return $this->numberOfTranslations;
    }

    /**
     * @return Collection<int, Translation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    public function setNumberOfIcons(int $numberOfIcons): self
    {
        $this->numberOfIcons = Validator::validateInteger($numberOfIcons);
        return $this;
    }

    public function getNumberOfIcons(): int
    {
        return $this->numberOfIcons;
    }

    /**
     * @return Collection<int, Icon>
     */
    public function getIcons(): Collection
    {
        return $this->icons;
    }
}
