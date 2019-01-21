<?php

declare(strict_types=1);

namespace FactorioItemBrowser\Api\Database\Entity;

/**
 * The entity class of the ModDependency database table.
 *
 * @author BluePsyduck <bluepsyduck@gmx.com>
 * @license http://opensource.org/licenses/GPL-3.0 GPL v3
 */
class ModDependency
{
    /**
     * The mod with the dependency.
     * @var Mod
     */
    protected $mod;

    /**
     * The required mod.
     * @var Mod
     */
    protected $requiredMod;

    /**
     * The required version of the mod.
     * @var string
     */
    protected $requiredVersion = '';

    /**
     * The type of the dependency.
     * @var string
     */
    protected $type = '';

    /**
     * Initializes the entity.
     * @param Mod $mod
     * @param Mod $requiredMod
     */
    public function __construct(Mod $mod, Mod $requiredMod)
    {
        $this->mod = $mod;
        $this->requiredMod = $requiredMod;
    }

    /**
     * Sets the mod with the dependency.
     * @param Mod $mod
     * @return $this Implementing fluent interface.
     */
    public function setMod(Mod $mod)
    {
        $this->mod = $mod;
        return $this;
    }

    /**
     * Returns the mod with the dependency.
     * @return Mod
     */
    public function getMod(): Mod
    {
        return $this->mod;
    }

    /**
     * Sets the required mod.
     * @param Mod $requiredMod
     * @return $this Implementing fluent interface.
     */
    public function setRequiredMod(Mod $requiredMod)
    {
        $this->requiredMod = $requiredMod;
        return $this;
    }

    /**
     * Returns the required mod.
     * @return Mod
     */
    public function getRequiredMod(): Mod
    {
        return $this->requiredMod;
    }

    /**
     * Sets the required version of the mod.
     * @param string $requiredVersion
     * @return $this Implementing fluent interface.
     */
    public function setRequiredVersion(string $requiredVersion)
    {
        $this->requiredVersion = $requiredVersion;
        return $this;
    }

    /**
     * Returns the required version of the mod.
     * @return string
     */
    public function getRequiredVersion(): string
    {
        return $this->requiredVersion;
    }

    /**
     * Sets the type of the dependency.
     * @param string $type
     * @return $this Implementing fluent interface.
     */
    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Returns the type of the dependency.
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
