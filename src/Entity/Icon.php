<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\Table;
use FactorioItemBrowser\Api\Database\Type\EnumTypeEntityType;

/**
 * The entity of the icon database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
#[Entity]
#[Table(options: [
    'charset' => 'utf8mb4',
    'collate' => 'utf8mb4_bin',
    'comment' => 'The table holding the icons of the items and recipes.',
])]
class Icon
{
    #[Id]
    #[ManyToOne(targetEntity: Combination::class)]
    #[JoinColumn(name: 'combinationId', nullable: false)]
    protected Combination $combination;

    #[Id]
    #[Column(type: EnumTypeEntityType::NAME, options: ['comment' => "The type of the icon's prototype."])]
    private string $type = '';

    #[Id]
    #[Column(length: 255, options: [
        'charset' => 'utf8mb4',
        'collate' => 'utf8mb4_bin',
        'comment' => "The name of the icon's prototype.",
    ])]
    private string $name = '';

    #[ManyToOne(targetEntity: IconImage::class)]
    #[JoinColumn(name: 'imageId', nullable: false)]
    private IconImage $image;

    public function setCombination(Combination $combination): self
    {
        $this->combination = $combination;
        return $this;
    }

    public function getCombination(): Combination
    {
        return $this->combination;
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
        $this->name = $name;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setImage(IconImage $image): self
    {
        $this->image = $image;
        return $this;
    }

    public function getImage(): IconImage
    {
        return $this->image;
    }
}
