<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\InverseJoinColumn;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\ManyToMany;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Ramsey\Uuid\Doctrine\UuidBinaryType;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity representing the Combination database table.
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
    #[Column(type: UuidBinaryType::NAME, options: ['comment' => 'The internal id of the combination.'])]
    private UuidInterface $id;

    #[Column(type: 'timestamp', options: ['comment' => 'The time when the combination was imported.'])]
    private DateTimeInterface $importTime;

    #[Column(type: 'timestamp', options: ['comment' => 'The time when the combination was last used by a visitor.'])]
    private DateTimeInterface $lastUsageTime;

    #[Column(type: 'timestamp', nullable: true, options: [
        'comment' => 'The last time this combination was checked for an update.',
    ])]
    private ?DateTimeInterface $lastUpdateCheckTime = null;

    #[Column(type: UuidBinaryType::NAME, nullable: true, options: [
        'comment' => 'The hash representing the mod versions used when the combination was last updated.',
    ])]
    private ?UuidInterface $lastUpdateHash = null;

    /** @var Collection<int, Mod> */
    #[ManyToMany(targetEntity: Mod::class)]
    #[JoinTable(name: 'CombinationXMod')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'modId', nullable: false)]
    private Collection $mods;

    /** @var Collection<int, Item> */
    #[ManyToMany(targetEntity: Item::class)]
    #[JoinTable(name: 'CombinationXItem')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'itemId', nullable: false)]
    private Collection $items;

    /** @var Collection<int, Machine> */
    #[ManyToMany(targetEntity: Machine::class)]
    #[JoinTable(name: 'CombinationXMachine')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'machineId', nullable: false)]
    private Collection $machines;

    /** @var Collection<int, Recipe> */
    #[ManyToMany(targetEntity: Recipe::class)]
    #[JoinTable(name: 'CombinationXRecipe')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'recipeId', nullable: false)]
    private Collection $recipes;

    /** @var Collection<int, Translation> */
    #[ManyToMany(targetEntity: Translation::class)]
    #[JoinTable(name: 'CombinationXTranslation')]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    #[InverseJoinColumn(name: 'translationId', nullable: false)]
    private Collection $translations;

    /** @var Collection<int, Icon> */
    #[OneToMany(mappedBy: 'combination', targetEntity: Icon::class)]
    private Collection $icons;

    public function __construct()
    {
        $this->mods = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->recipes = new ArrayCollection();
        $this->machines = new ArrayCollection();
        $this->translations = new ArrayCollection();
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

    /**
     * @return Collection<int, Mod>
     */
    public function getMods(): Collection
    {
        return $this->mods;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
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

    /**
     * @return Collection<int, Translation>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    /**
     * @return Collection<int, Icon>
     */
    public function getIcons(): Collection
    {
        return $this->icons;
    }
}
