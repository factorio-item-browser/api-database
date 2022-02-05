<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Constant\CustomTypes;
use FactorioItemBrowser\Api\Database\Helper\Validator;

/**
 * The entity representing a research ingredient of a technology.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_bin',
    'comment' => 'The table holding the research ingredients of technologies.',
])]
class TechnologyIngredient
{
    private const FACTOR_AMOUNT = 1000;

    #[Id]
    #[ManyToOne(targetEntity: TechnologyData::class, inversedBy: 'ingredients')]
    #[JoinColumn(name: 'technologyDataId', nullable: false)]
    private TechnologyData $technologyData;

    #[Id]
    #[Column(name: '`order`', type: CustomTypes::TINYINT, options: [
        'unsigned' => true,
        'comment' => 'The order of the ingredient in the technology.',
    ])]
    private int $order = 0;

    #[ManyToOne(targetEntity: Item::class, fetch: 'EAGER')]
    #[JoinColumn(name: 'itemId', nullable: false)]
    private Item $item;

    #[Column(type: Types::INTEGER, options: [
        'unsigned' => true,
        'comment' => 'The amount required for the technology.',
    ])]
    private int $amount = 0;

    public function setTechnologyData(TechnologyData $technologyData): self
    {
        $this->technologyData = $technologyData;
        return $this;
    }

    public function getTechnologyData(): TechnologyData
    {
        return $this->technologyData;
    }

    public function setOrder(int $order): self
    {
        $this->order = Validator::validateTinyInteger($order);
        return $this;
    }

    public function getOrder(): int
    {
        return $this->order;
    }

    public function setItem(Item $item): self
    {
        $this->item = $item;
        return $this;
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = Validator::validateInteger((int) ($amount * self::FACTOR_AMOUNT));
        return $this;
    }

    public function getAmount(): float
    {
        return $this->amount / self::FACTOR_AMOUNT;
    }
}
