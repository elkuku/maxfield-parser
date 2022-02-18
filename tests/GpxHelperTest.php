<?php

namespace App\Tests;

use Elkuku\MaxfieldParser\GpxHelper;
use Elkuku\MaxfieldParser\MaxfieldParser;
use PHPUnit\Framework\TestCase;

class GpxHelperTest extends TestCase
{
    private string $testDir = __DIR__.'/testfiles';

    public function testGetRouteTrackGpx(): void
    {
        $gpxExpected = trim(file_get_contents($this->testDir.'/test.gpx'));

        $gpxHelper = new GpxHelper();

        $gpx = $gpxHelper->getRouteTrackGpx(new MaxfieldParser($this->testDir.'/12345'));

        self::assertEquals($gpxExpected, $gpx);
    }

    public function testGetWaypointsGpx(): void
    {
        $gpxExpected = trim(file_get_contents($this->testDir.'/test_waypoints.gpx')?:'');

        $gpxHelper = new GpxHelper();

        $gpx = $gpxHelper->getWaypointsGpx(new MaxfieldParser($this->testDir.'/12345'));

        self::assertEquals($gpxExpected, $gpx);
    }

    public function testGetRouteGpx(): void
    {
        $gpxExpected = trim(file_get_contents($this->testDir.'/test_route.gpx')?:'');

        $gpxHelper = new GpxHelper();

        $gpx = $gpxHelper->getRouteGpx(new MaxfieldParser($this->testDir.'/12345'));

        self::assertEquals($gpxExpected, $gpx);
    }

    public function testGetTrackGpx(): void
    {
        $gpxExpected = trim(file_get_contents($this->testDir.'/test_track.gpx')?:'');

        $gpxHelper = new GpxHelper();

        $gpx = $gpxHelper->getTrackGpx(new MaxfieldParser($this->testDir.'/12345'));

        self::assertEquals($gpxExpected, $gpx);
    }
}
