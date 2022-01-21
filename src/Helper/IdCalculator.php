<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Helper;

use FactorioItemBrowser\Api\Database\Attribute\IncludeInIdCalculation;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use ReflectionClass;

/**
 * The class helping with calculating IDs for the entities.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class IdCalculator
{
    /**
     * Calculates the ID for the provided object.
     * @param object $object
     * @return UuidInterface
     */
    public function calculateId(object $object): UuidInterface
    {
        $data = $this->extractDataFromObject($object);
        return Uuid::fromString(hash('md5', (string) json_encode($data)));
    }

    private function extractData(mixed $value): mixed
    {
        if (is_iterable($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $result[$k] = $this->extractData($v);
            }
            return $result;
        }

        if (is_object($value)) {
            return $this->extractDataFromObject($value);
        }

        return $value;
    }

    /**
     * @param object $object
     * @return array<string, mixed>
     */
    private function extractDataFromObject(object $object): array
    {
        $result = [];
        foreach ((new ReflectionClass($object))->getProperties() as $property) {
            $attribute = $property->getAttributes(IncludeInIdCalculation::class)[0] ?? null;
            if ($attribute !== null) {
                $result[$property->getName()] = $this->extractData($property->getValue($object));
            }
        }
        return $result;
    }
}
