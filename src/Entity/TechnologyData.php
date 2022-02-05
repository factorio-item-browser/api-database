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
use Doctrine\ORM\Mapping\OrderBy;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Attribute\IncludeInIdCalculation;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Helper\Validator;
use Ramsey\Uuid\UuidInterface;

/**
 * The entity representing the actual data of a technology.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the actual data of technologies.',
])]
class TechnologyData implements EntityWithId
{
    private const FACTOR_TIME = 1000;

    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the technology data.'])]
    private UuidInterface $id;

    /** @var Collection<int, TechnologyIngredient> */
    #[OneToMany(mappedBy: 'technologyData', targetEntity: TechnologyIngredient::class, cascade: ['all'])]
    #[OrderBy(['order' => 'ASC'])]
    #[IncludeInIdCalculation]
    private Collection $ingredients;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The number of researches required to unlock the technology.',
    ])]
    #[IncludeInIdCalculation]
    private int $count = 0;

    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_bin',
        'comment' => 'The formula to calculate the research count in case the technology has multiple levels.',
    ])]
    #[IncludeInIdCalculation]
    private string $countFormula = '';

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The time required for each research.',
    ])]
    #[IncludeInIdCalculation]
    private int $time = 0;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The level of the technology.',
    ])]
    #[IncludeInIdCalculation]
    private int $level = 0;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The maximal level of the technology.',
    ])]
    #[IncludeInIdCalculation]
    private int $maxLevel = 0;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
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

    /**
     * @return Collection<int, TechnologyIngredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function setCount(int $count): self
    {
        $this->count = Validator::validateInteger($count);
        return $this;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCountFormula(string $countFormula): self
    {
        $this->countFormula = Validator::validateString($countFormula);
        return $this;
    }

    public function getCountFormula(): string
    {
        return $this->countFormula;
    }

    public function setTime(float $time): self
    {
        $this->time = Validator::validateInteger((int) ($time * self::FACTOR_TIME));
        return $this;
    }

    public function getTime(): float
    {
        return $this->time / self::FACTOR_TIME;
    }

    public function setLevel(int $level): self
    {
        $this->level = Validator::validateInteger($level);
        return $this;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function setMaxLevel(int $maxLevel): self
    {
        $this->maxLevel = Validator::validateInteger($maxLevel);
        return $this;
    }

    public function getMaxLevel(): int
    {
        return $this->maxLevel;
    }
}
