<?php

namespace Phpactor\TextDocument\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\TextDocument\Location;
use Phpactor\TextDocument\Locations;
use RuntimeException;

class LocationsTest extends TestCase
{
    public function testContainsLocations()
    {
        $locations = new Locations([
            Location::fromPathAndOffset('/path/to.php', 12),
            Location::fromPathAndOffset('/path/to.php', 13)
        ]);
        $this->assertCount(2, $locations);
    }

    public function testIsCountable()
    {
        $locations = new Locations([
            Location::fromPathAndOffset('/path/to.php', 12),
            Location::fromPathAndOffset('/path/to.php', 13)
        ]);
        $this->assertEquals(2, $locations->count());
    }

    public function testExceptionIfFirstNotAvailable()
    {
        $this->expectException(RuntimeException::class);
        $locations = new Locations([
        ]);
        $locations->first();
    }
}
