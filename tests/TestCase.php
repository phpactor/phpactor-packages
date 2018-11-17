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
}
