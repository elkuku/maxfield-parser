<?php

namespace App\Tests;

use Elkuku\MaxfieldParser\GpxHelper;
use Elkuku\MaxfieldParser\MaxfieldParser;
use PHPUnit\Framework\TestCase;

class MaxfieldParserTest extends TestCase
{
    public function testGpxParserCanParse(): void
    {
        $testDir = __DIR__.'/testfiles';
        $gpxExpected = file_get_contents($testDir.'/test.gpx');

        $gpxHelper = new GpxHelper();

        $gpx = $gpxHelper->getRouteTrackGpx(new MaxfieldParser($testDir.'/12345'));

        self::assertEquals($gpxExpected, $gpx);
    }
}
