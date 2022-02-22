<?php

namespace App\Tests;

use Elkuku\MaxfieldParser\Exception\FileNotFoundException;
use Elkuku\MaxfieldParser\MaxfieldParser;
use PHPUnit\Framework\TestCase;
use UnexpectedValueException;

class MaxfieldParserTest extends TestCase
{
    private string $testDir = __DIR__.'/testfiles';

    public function testParseMaxfield(): void
    {
        $parser = new MaxfieldParser($this->testDir.'/12345');

        $maxfield = $parser->parse();

        self::assertCount(15, $maxfield->keyPrep->getWayPoints());
        self::assertCount(32, $maxfield->links);
        self::assertCount(1, $maxfield->agentsInfo);
        self::assertCount(32, $maxfield->agentsInfo[0]->links);
        self::assertCount(15, $maxfield->agentsInfo[0]->keys->getWayPoints());
        self::assertCount(40, $maxfield->steps);
    }

    public function testParsePortalsEmptyLine(): void
    {
        $parser = new MaxfieldParser($this->testDir.'/errors');

        $waypoints = $parser->getWayPoints('portals/empty_line');

        self::assertCount(3, $waypoints);
    }

    public function testParsePortalsFishyLine(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Fishy CSV line');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->getWayPoints('portals/fishy_line');
    }

    public function testParsePortalsFishyLocLine(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Fishy CSV loc line');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->getWayPoints('portals/fishy_loc_line');
    }

    public function testParsePortalsFishyCoordsLine(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Fishy CSV coords line');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->getWayPoints('portals/fishy_coords_line');
    }

    public function testParseKeyprepFishy(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Fishy CSV line has 6 parts instead of 5');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->getKeyPrep('keyprep');
    }

    public function testParseAssignmentFishy(): void
    {
        $this->expectException(UnexpectedValueException::class);
        $this->expectExceptionMessage('Error parsing CSV file');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->getLinks('agent_assignments');
    }

    public function testMissingFile(): void
    {
        $this->expectException(FileNotFoundException::class);
        $this->expectExceptionMessageMatches('/File not found/');

        $parser = new MaxfieldParser($this->testDir.'/errors');

        $parser->getLinks('ERROR');
    }
}
