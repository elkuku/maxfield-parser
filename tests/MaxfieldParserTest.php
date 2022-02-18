<?php

namespace App\Tests;

use Elkuku\MaxfieldParser\MaxfieldParser;
use PHPUnit\Framework\TestCase;

class MaxfieldParserTest extends TestCase
{
    private string $testDir = __DIR__.'/testfiles';

    public function testParsePortalsEmptyLine(): void
    {
        $parser = new MaxfieldParser($this->testDir.'/errors');

        $waypoints = $parser->parseWayPointsFile('portals/empty_line');

        self::assertCount(3, $waypoints);
    }

    public function testParsePortalsFishyLine(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Fishy CSV line');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->parseWayPointsFile('portals/fishy_line');
    }

    public function testParsePortalsFishyLocLine(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Fishy CSV loc line');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->parseWayPointsFile('portals/fishy_loc_line');
    }

    public function testParsePortalsFishyCoordsLine(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Fishy CSV coords line');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->parseWayPointsFile('portals/fishy_coords_line');
    }

    public function testParseKeyprepFishy(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Fishy CSV line has 6 parts instead of 5');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->getKeyPrep('keyprep');
    }

    public function testParseAssignmentFishy(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('Error parsing CSV file');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->getLinks('agent_assignments');
    }

    public function testMissingFile(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessageMatches('/File not found/');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->getLinks('ERROR');
    }
}
