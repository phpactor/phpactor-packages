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

    public function __construct(string $name, string $version, string $description, array $dependencies = [])
    {
        $this->name = $name;
        $this->version = $version;
        $this->description = $description;
        $this->dependencies = $dependencies;
    }

    public static function fromPackage(CompletePackageInterface $package)
    {
        $dependencies = array_map(function (Link $link) {
            return $link->getTarget();
        }, $package->getRequires());
        return new self(
            $package->getName(),
            $package->getFullPrettyVersion(),
            $package->getDescription() ?: '',
            $dependencies
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
}
