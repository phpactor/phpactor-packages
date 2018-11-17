<?php

namespace Phpactor\Extension\ExtensionManager\Model;

use Composer\Package\CompletePackageInterface;

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

    public function __construct(string $name, string $version, string $description, array $dependencies)
    {
        $this->name = $name;
        $this->version = $version;
        $this->description = $description;
        $this->dependencies = $dependencies;
    }

    public static function fromPackage(CompletePackageInterface $package)
    {
        return new self($package->getName(), $package->getFullPrettyVersion(), $package->getDescription());
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
}
