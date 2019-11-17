<?php

namespace Phpactor\TestUtils\Tests\Integration\Workspace;

use Phpactor\TestUtils\Tests\Integration\IntegrationTestCase;
use Phpactor\TestUtils\Workspace;
use InvalidArgumentException;

class WorkspaceTest extends IntegrationTestCase
{
    public function setUp()
    {
        $this->workspace = Workspace::create($this->workspaceDir());
        $this->workspace->reset();
    }

    public function testBuild()
    {
        $manifest = <<<'EOT'
// File: Foobar.php
<?php

class Foobar
{
}
// File: Foobar/Barfoo.php
<?php

namespace Foobar;

class Barfoo
{
}
// File: Expected.php
Hello World
EOT
        ;

        $this->workspace->loadManifest($manifest);

        $this->assertTrue($this->workspace->exists('Foobar.php'));
        $this->assertTrue($this->workspace->exists('Foobar/Barfoo.php'));
        $this->assertFalse($this->workspace->exists('Foobar/Barboo.php'));
        $this->assertEquals('Hello World', $this->workspace->getContents('Expected.php'));
    }

    public function testGetContentsNotExist()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('File "barbarbarbar" does not exist');

        $this->workspace->getContents('barbarbarbar');
    }

    public function testReset()
    {
        $this->workspace->reset();
        touch($this->workspace->path('Foobar.php'));
        mkdir($this->workspace->path('Barfoo'));

        touch($this->workspace->path('Barfoo/Foobar.php'));
        touch($this->workspace->path('Barfoo/BazBoo.php'));

        $this->workspace->reset();

        $this->assertFalse($this->workspace->exists('Foobar.php'));
        $this->assertFalse($this->workspace->exists('Barfoo/Foobar.php'));
        $this->assertFalse($this->workspace->exists('Barfoo/Bazboo.php'));
    }

    public function testMkdir()
    {
        $this->workspace->mkdir('foobar');
        $this->assertTrue($this->workspace->exists('foobar'));
        $this->assertFalse($this->workspace->exists('barfoo'));
    }

    public function testPutFileContents()
    {
        $this->workspace->put('foobar', 'foobar contents');
        $this->assertTrue($this->workspace->exists('foobar'));
        $this->assertContains('foobar contents', $this->workspace->getContents('foobar'));
    }
}
