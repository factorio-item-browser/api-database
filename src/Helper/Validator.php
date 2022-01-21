<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Helper;

/**
 * The class helping with validating values.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class Validator
{
    public static function validateInteger(int $value): int
    {
        return min(max($value, 0), 4294967295);
    }

    public static function validateTinyInteger(int $value): int
    {
        return min(max($value, 0), 255);
    }

    /**
     * Limits the provided string to the provided length, defaulting to the maximum of 255 for a VARCHAR.
     */
    public static function validateString(string $value, int $length = 255): string
    {
        return substr(trim($value), 0, $length);
    }

    /**
     * Limits the provided TEXT value to its maximum length.
     */
    public static function validateText(string $value): string
    {
        return substr(trim($value), 0, 65535);
    }
}
