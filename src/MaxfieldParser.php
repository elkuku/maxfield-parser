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
                name: trim($parts[0]),
                lat: (float)$coords[0],
                lon: (float)$coords[1],
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
                throw new UnexpectedValueException(
                    sprintf(
                        'Fishy CSV line has %d parts instead of 5',
                        count($parts)
                    )
                );
            }

            $keyPrep->addWayPoint(
                new WayPointPrep(
                    mapNo: (int)$parts[3],
                    name: trim($parts[4]),
                    keysNeeded: (int)$parts[0]
                )
            );
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

            $links[] = new AgentLink(
                linkNum: (int)$parts[0],
                agentNum: (int)$parts[1],
                isEarly: (bool)strpos($parts[0], '*'),
                originNum: (int)$parts[2],
                originName: trim($parts[3]),
                destinationNum: (int)$parts[4],
                destinationName: trim($parts[5])
            );
        }

        usort(
            $links,
            static function ($a, $b): int {
                return $a->linkNum > $b->linkNum ? 1 : 0;
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

        $contents = file_get_contents($path);

        if (!$contents) {
            throw new UnexpectedValueException('Error opening file.: '.$path);
        }

        return $contents;
    }
}
