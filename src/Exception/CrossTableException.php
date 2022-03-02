<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Exception;

use Exception;
use Throwable;

/**
 * The exception thrown when something with managing a cross-table failed.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class CrossTableException extends Exception
{
    private const MESSAGE = 'Exception while managing the cross-table of %s: %s';

    public function __construct(string $entityClass, string $message, ?Throwable $previous = null)
    {
        parent::__construct(sprintf(self::MESSAGE, $entityClass, $message), 500, $previous);
    }
}
