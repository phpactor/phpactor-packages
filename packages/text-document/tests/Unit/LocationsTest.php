<?php

namespace Phpactor\TextDocument\Tests\Unit;

use PHPUnit\Framework\TestCase;
use Phpactor\TextDocument\Location;
use Phpactor\TextDocument\Locations;

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
}
