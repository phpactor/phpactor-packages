<?php

namespace Phpactor\Extension\ExtensionManager\Model;

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
     * @var int
     */
    private $state;

    /**
     * @var string
     */
    private $className;

    public function __construct(
        string $name,
        string $version,
        string $className,
        string $description,
        array $dependencies = [],
        int $state = ExtensionState::STATE_NOT_INSTALLED
    ) {
        $this->name = $name;
        $this->version = $version;
        $this->description = $description;
        $this->dependencies = $dependencies;
        $this->state = $state;
        $this->className = $className;
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

    public function state(): ExtensionState
    {
        return new ExtensionState($this->state);
    }

    public function className(): string
    {
        return $this->className;
    }
}
