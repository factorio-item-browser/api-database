<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Data;

use BluePsyduck\Common\Data\DataContainer;

/**
 * The class representing partial translation data.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class TranslationData implements DataInterface
{
    /**
     * The locale of the translation.
     * @var string
     */
    protected $locale = '';

    /**
     * The type of the translation.
     * @var string
     */
    protected $type = '';

    /**
     * The name of the translation.
     * @var string
     */
    protected $name = '';

    /**
     * The actual translation.
     * @var string
     */
    protected $value = '';

    /**
     * The translated description.
     * @var string
     */
    protected $description = '';

    /**
     * Whether this translation is duplicated by the recipe.
     * @var bool
     */
    protected $isDuplicatedByRecipe = false;

    /**
     * Whether this translation is duplicated by the machine.
     * @var bool
     */
    protected $isDuplicatedByMachine = false;

    /**
     * The order of the translation.
     * @var int
     */
    protected $order = 0;

    /**
     * Sets the locale of the translation.
     * @param string $locale
     * @return $this
     */
    public function setLocale(string $locale)
    {
        $this->locale = $locale;
        return $this;
    }

    /**
     * Returns the locale of the translation.
     * @return string
     */
    public function getLocale(): string
    {
        return $this->locale;
    }

    /**
     * Sets the type of the translation.
     * @param string $type
     * @return $this
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the type of the translation.
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Sets the name of the translation.
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Returns the name of the translation.
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets the value of the translation.
     * @param string $value
     * @return $this
     */
    public function setValue(string $value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * Returns the value of the translation.
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Sets the translated description.
     * @param string $description
     * @return $this
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Returns the translated description.
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets whether this translation is duplicated by the recipe.
     * @param bool $isDuplicatedByRecipe
     * @return $this
     */
    public function setIsDuplicatedByRecipe(bool $isDuplicatedByRecipe)
    {
        $this->isDuplicatedByRecipe = $isDuplicatedByRecipe;
        return $this;
    }

    /**
     * Returns whether this translation is duplicated by the recipe.
     * @return bool
     */
    public function getIsDuplicatedByRecipe(): bool
    {
        return $this->isDuplicatedByRecipe;
    }

    /**
     * Sets whether this translation is duplicated by the machine.
     * @param bool $isDuplicatedByMachine
     * @return $this
     */
    public function setIsDuplicatedByMachine(bool $isDuplicatedByMachine)
    {
        $this->isDuplicatedByMachine = $isDuplicatedByMachine;
        return $this;
    }

    /**
     * Returns whether this translation is duplicated by the machine.
     * @return bool
     */
    public function getIsDuplicatedByMachine(): bool
    {
        return $this->isDuplicatedByMachine;
    }

    /**
     * Sets the order of the translation.
     * @param int $order
     * @return $this
     */
    public function setOrder(int $order)
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Returns the order of the translation.
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * Returns the keys to identify identical data.
     * @return array|string[]
     */
    public function getKeys(): array
    {
        return [
            $this->locale,
            $this->type,
            $this->name,
        ];
    }

    /**
     * Creates a new instance from the specified data array.
     * @param array $dataArray
     * @return self
     */
    public static function createFromArray(array $dataArray): self
    {
        $data = new DataContainer($dataArray);

        $result = new self();
        $result->setLocale($data->getString('locale'))
               ->setType($data->getString('type'))
               ->setName($data->getString('name'))
               ->setValue($data->getString('value'))
               ->setDescription($data->getString('description'))
               ->setIsDuplicatedByRecipe($data->getBoolean('isDuplicatedByRecipe'))
               ->setIsDuplicatedByMachine($data->getBoolean('isDuplicatedByMachine'))
               ->setOrder($data->getInteger('order'));
        return $result;
    }
}
