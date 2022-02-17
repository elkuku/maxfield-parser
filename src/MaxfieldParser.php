<?php

namespace Elkuku\MaxfieldParser;

use Elkuku\MaxfieldParser\Type\AgentLink;
use Elkuku\MaxfieldParser\Type\KeyPrep;
use Elkuku\MaxfieldParser\Type\Waypoint;
use Elkuku\MaxfieldParser\Type\WayPointPrep;
use UnexpectedValueException;
use function count;

class MaxfieldParser
{
    public function __construct(
        private readonly string $rootDir
    ) {
    }

    public function getKeyPrep(string $item = ''): KeyPrep
    {
        return $this->parseKeyPrepFileCsv(
            $this->getFileContents('key_preparation.csv', $item)
        );
    }

    /**
     * @return AgentLink[]
     */
    public function getLinks(string $item = ''): array
    {
        return $this->parseCsvLinks(
            $this->getFileContents('agent_assignments.csv', $item)
        );
    }

    /**
     * @return Waypoint[]
     */
    public function parseWayPointsFile(string $item = ''): array
    {
        $contents = $this->getFileContents('portals.txt', $item);
        $lines = explode("\n", $contents);
        $wayPoints = [];

        foreach ($lines as $line) {
            $l = trim($line);

            if (!$l) {
                continue;
            }

            $parts = explode(';', $l);

            if (2 !== count($parts) && 3 !== count($parts)) {
                throw new UnexpectedValueException('Fishy CSV line');
            }

            $loc = explode('pll=', $parts[1]);

            if (2 !== count($loc)) {
                throw new UnexpectedValueException('Fishy CSV loc line');
            }

            $coords = explode(',', $loc[1]);

            if (2 !== count($coords)) {
                throw new UnexpectedValueException('Fishy CSV coords line');
            }

            $wayPoints[] = new Waypoint(
                trim($parts[0]),
                $coords[0],
                $coords[1],
            );
        }

        return $wayPoints;
    }

    private function parseKeyPrepFileCsv(string $contents): KeyPrep
    {
        $keyPrep = new KeyPrep();

        $lines = explode("\n", $contents);

        foreach ($lines as $i => $line) {
            $l = trim($line);

            if (!$l
                || $i === 0
                || str_starts_with($l, 'Keys Needed')
                || str_starts_with($l, 'Number of missing')
            ) {
                continue;
            }

            $parts = explode(',', $l);

            if (5 !== count($parts)) {
                continue;
            }

            $p = new WayPointPrep();

            $p->keysNeeded = (int)$parts[0];
            $p->mapNo = (int)$parts[3];
            $p->name = trim($parts[4]);

            $keyPrep->addWayPoint($p);
        }

        return $keyPrep;
    }

    /**
     * @return AgentLink[]
     */
    private function parseCsvLinks(string $contents): array
    {
        $links = [];

        $lines = explode("\n", $contents);

        foreach ($lines as $i => $line) {
            if (0 === $i || !$line) {
                continue;
            }

            $parts = explode(',', $line);

            if (6 !== count($parts)) {
                throw new UnexpectedValueException('Error parsing CSV file');
            }

            $link = new AgentLink();

            $link->linkNum = (int)$parts[0];
            $link->isEarly = (bool)strpos($parts[0], '*');
            $link->agentNum = (int)$parts[1];
            $link->originNum = (int)$parts[2];
            $link->originName = trim($parts[3]);
            $link->destinationNum = (int)$parts[4];
            $link->destinationName = trim($parts[5]);

            $links[] = $link;
        }

        usort(
            $links,
            static function ($a, $b) {
                return $a->linkNum > $b->linkNum;
            }
        );

        return $links;
    }

    private function getFileContents(
        string $fileName,
        string $item = ''
    ): string {
        $path = $item
            ? $this->rootDir.'/'.$item.'/'.$fileName
            : $this->rootDir.'/'.$fileName;

        if (false === file_exists($path)) {
            throw new UnexpectedValueException('File not found.: '.$path);
        }

        return file_get_contents($path);
    }
}
