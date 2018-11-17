<?php

namespace Phpactor\Extension\ExtensionManager\Tests;

use PHPUnit\Framework\TestCase as PHPUnitTestCase;
use Phpactor\TestUtils\Workspace;

class TestCase extends PHPUnitTestCase
{
    /**
     * @var Workspace
     */
    protected $workspace;

    public function setUp()
    {
        $this->workspace = Workspace::create(__DIR__ . '/Workspace');
        $this->workspace->reset();
    }

    public function tearDown()
    {
//        $this->workspace->reset();
    }

    public function loadProject(string $name, string $manifest)
    {
        $projectWorkspace = Workspace::create($this->workspace->path($name));
        $projectWorkspace->reset();
        $projectWorkspace->loadManifest($manifest);
        $dir = getcwd();
        chdir($projectWorkspace->path('/'));
        exec('git init');
        exec('git add -A');
        exec('git commit -m "first"');
        chdir($dir);
    }
}
