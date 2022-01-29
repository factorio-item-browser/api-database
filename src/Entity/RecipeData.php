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
 * The entity representing the actual data of a recipe.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the actual recipe data.',
])]
class RecipeData implements EntityWithId
{
    private const FACTOR_TIME = 1000;

    #[Id]
    #[Column(type: CustomTypes::UUID, options: ['comment' => 'The internal id of the recipe data.'])]
    private UuidInterface $id;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The required time in milliseconds to craft the recipe.',
    ])]
    #[IncludeInIdCalculation]
    private int $time = 0;

    /** @var Collection<int, RecipeIngredient> */
    #[OneToMany(mappedBy: 'recipeData', targetEntity: RecipeIngredient::class, cascade: ['all'])]
    #[OrderBy(['order' => 'ASC'])]
    #[IncludeInIdCalculation]
    private Collection $ingredients;

    /** @var Collection<int, RecipeProduct> */
    #[OneToMany(mappedBy: 'recipeData', targetEntity: RecipeProduct::class, cascade: ['all'])]
    #[OrderBy(['order' => 'ASC'])]
    #[IncludeInIdCalculation]
    private Collection $products;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
        $this->products = new ArrayCollection();
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

    public function setTime(float $time): self
    {
        $this->time = Validator::validateInteger((int) ($time * self::FACTOR_TIME));
        return $this;
    }

    public function getTime(): float
    {
        return $this->time / self::FACTOR_TIME;
    }

    /**
     * @return Collection<int, RecipeIngredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    /**
     * @return Collection<int, RecipeProduct>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }
}
