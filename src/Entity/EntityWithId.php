<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

use Ramsey\Uuid\UuidInterface;

/**
 * The interface signaling that the entity is aware of an id.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
interface EntityWithId
{
    /**
     * Returns the internal id of the entity.
     */
    public function getId(): UuidInterface;
}
