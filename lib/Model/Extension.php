<?php

namespace Phpactor\Extension\ExtensionManager\Model;

use Composer\Package\CompletePackageInterface;
use Composer\Package\Link;

class Extension
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $version;

    /**
     * @var string
     */
    private $description;

    /**
     * @var array
     */
    private $dependencies;

    /**
     * @var bool
     */
    private $isPrimary;

    public function __construct(
        string $name,
        string $version,
        string $description,
        array $dependencies = [],
        bool $isPrimary = false
    ) {
        $this->name = $name;
        $this->version = $version;
        $this->description = $description;
        $this->dependencies = $dependencies;
        $this->isPrimary = $isPrimary;
    }

    public static function fromPackage(CompletePackageInterface $package, bool $isPrimary = false)
    {
        $dependencies = array_map(function (Link $link) {
            return $link->getTarget();
        }, $package->getRequires());
        return new self(
            $package->getName(),
            $package->getFullPrettyVersion(),
            $package->getDescription() ?: '',
            $dependencies,
            $isPrimary
        );
    }

    public function name(): string
    {
        return $this->name;
    }

    public function version(): string
    {
        return $this->version;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function dependencies(): array
    {
        return $this->dependencies;
    }

    public function isPrimary(): bool
    {
        return $this->isPrimary;
    }
}
