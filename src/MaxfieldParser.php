<?php

namespace Elkuku\MaxfieldParser;

use Elkuku\MaxfieldParser\Exception\FileNotFoundException;
use Elkuku\MaxfieldParser\Type\AgentInfo;
use Elkuku\MaxfieldParser\Type\AgentLink;
use Elkuku\MaxfieldParser\Type\KeyPrep;
use Elkuku\MaxfieldParser\Type\MaxField;
use Elkuku\MaxfieldParser\Type\Step;
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

    public function parse(string $subPath = ''): MaxField
    {
        $maxField = new MaxField();

        $maxField->keyPrep = $this->getKeyPrep($subPath);
        $maxField->agentsInfo = $this->getAgentsInfo($subPath);

        $maxField->links = $this->getLinks($subPath);
        $maxField->steps = $this->calculateSteps($maxField->links);

        return $maxField;
    }

    public function getKeyPrep(string $subPath = ''): KeyPrep
    {
        return $this->parseKeyPrepFileCsv(
            $this->getFileContents('key_preparation.csv', $subPath)
        );
    }

    /**
     * @return AgentLink[]
     */
    public function getLinks(string $subPath = ''): array
    {
        return $this->parseCsvLinks(
            $this->getFileContents('agent_assignments.csv', $subPath)
        );
    }

    /**
     * @return Waypoint[]
     */
    public function getWayPoints(string $subPath = ''): array
    {
        $contents = $this->getFileContents('portals.txt', $subPath);
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

    /**
     * @return AgentInfo[]
     * @todo this returns an array where all the elements are almost the same :(
     *
     */
    private function getAgentsInfo(string $item): array
    {
        $agentsInfo = [];
        $links = $this->getLinks($item);
        $keys = $this->parseAgentKeyPrepFile($item);

        $numAgents = 1;
        foreach ($links as $link) {
            if ($link->agentNum > $numAgents) {
                $numAgents = $link->agentNum;
            }
        }

        for ($count = 1; $count <= $numAgents; $count++) {
            $info = new AgentInfo();

            $info->agentNumber = $count;
            $info->links = $links;
            $info->keys = $keys;

            $agentsInfo[] = $info;
        }

        return $agentsInfo;
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

    private function parseAgentKeyPrepFile(string $subPath = ''): KeyPrep
    {
        $keyPrep = new KeyPrep();

        $contents = $this->getFileContents(
            'agent_key_preparation.csv',
            $subPath,
        );

        $lines = explode("\n", $contents);

        foreach ($lines as $i => $line) {
            if (0 === $i || !$line) {
                continue;
            }

            $parts = explode(',', $line);

            if (4 !== \count($parts)) {
                throw new \UnexpectedValueException('Error parsing CSV file');
            }

            $wayPoint = new WayPointPrep();

            $wayPoint->agentNum = (int)$parts[0];
            $wayPoint->keysNeeded = (int)$parts[1];
            $wayPoint->mapNo = (int)$parts[2];
            $wayPoint->name = trim($parts[3]);

            $keyPrep->addWayPoint($wayPoint);
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

    /**
     * @throws \Elkuku\MaxfieldParser\Exception\FileNotFoundException
     */
    private function getFileContents(
        string $fileName,
        string $subPath = ''
    ): string {
        $path = $this->getRootPath($subPath).'/'.$fileName;

        if (false === file_exists($path)) {
            throw new FileNotFoundException('File not found.: '.$path);
        }

        $contents = file_get_contents($path);

        if (!$contents) {
            throw new UnexpectedValueException('Error opening file.: '.$path);
        }

        return $contents;
    }

    private function getRootPath(string $subPath = ''): string
    {
        return $subPath
            ? $this->rootDir.'/'.$subPath
            : $this->rootDir;
    }

    /**
     * This generates an array containing:
     *  - The "links"
     *  - The "moves" from one portal to the next.
     *
     * @param AgentLink[] $links
     *
     * @return Step[]
     */
    private function calculateSteps(array $links): array
    {
        $steps = [];

        foreach ($links as $i => $link) {
            if (($i > 0) && $link->originNum !== $links[$i - 1]->originNum) {
                $steps[] = new Step(
                    action: Step::TYPE_MOVE,
                    agentNum: $link->agentNum,
                    originNum: $links[$i - 1]->originNum,
                    originName: $links[$i - 1]->originName,
                    destinationNum: $link->originNum,
                    destinationName: $link->originName
                );
            }

            $steps[] = new Step(
                action: Step::TYPE_LINK,
                linkNum: $link->linkNum,
                agentNum: $link->agentNum,
                originNum: $link->originNum,
                originName: $link->originName,
                destinationNum: $link->destinationNum,
                destinationName: $link->destinationName
            );
        }

        return $steps;
    }
}
