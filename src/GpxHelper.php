<?php

namespace Elkuku\MaxfieldParser;

use Elkuku\MaxfieldParser\Type\AgentLink;

class GpxHelper
{
    public function getWaypointsGpx(string $item): string
    {
        $maxField = $this->maxFieldGenerator->getInfo($item);
        $wayPoints = $this->maxFieldGenerator->parseWayPointsFile($item);

        $xml = [];

        $xml[] = $this->getGpxHeader();

        foreach ($maxField->keyPrep->getWayPoints() as $wayPointAgent) {
            $wayPoint = $wayPoints[$wayPointAgent->mapNo];
            $xml[] = '<wpt lat="'.$wayPoint->getLat().'" lon="'
                .$wayPoint->getLon().'">';
            $xml[] = '  <name>'.$wayPoint->getName().'</name>';
            $xml[] = '  <desc>Farm keys: '.$wayPointAgent->keysNeeded.'</desc>';
            $xml[] = '</wpt>';
        }

        $xml[] = '</gpx>';

        return implode("\n", $xml);
    }


    public function getRouteTrackGpx(MaxfieldParser $maxfieldParser): string
    {
        // $maxField = $this->maxFieldGenerator->getInfo($item);
        // $wayPoints = $this->maxFieldGenerator->parseWayPointsFile($item);

        $keyprepPoints = $maxfieldParser->getKeyPrep();
        $wayPoints = $maxfieldParser->parseWayPointsFile();
        $links = $maxfieldParser->getLinks();

        $xml = [];

        $xml[] = $this->getGpxHeader();

        foreach ($keyprepPoints->getWayPoints() as $wayPointAgent) {
            $wayPoint = $wayPoints[$wayPointAgent->mapNo];
            $xml[] = '<wpt lat="'.$wayPoint->lat.'" lon="'
                .$wayPoint->lon.'">';
            $xml[] = '  <name>'.$wayPoint->name.'</name>';
            $xml[] = '  <desc>Farm keys: '.$wayPointAgent->keysNeeded.'</desc>';
            $xml[] = '</wpt>';
        }

        $xml[] = '<rte>';
        $xml[] = '<name>Routenname</name>';

        $steps = $this->calculateSteps($links);

        foreach ($steps as $step) {
            $origin = $wayPoints[$step->origin];
            $xml[] = '<rtept lat="'.$origin->lat.'"'
                .' lon="'.$origin->lon.'">';
            $xml[] = '<name>'.$origin->name.'</name>';
            $links = '';
            foreach ($step->destinations as $index) {
                $links .= 'Link: '.$wayPoints[$index]->name.'*BR*';
            }

            $xml[] = '<desc>'.$links.'</desc>';
            $xml[] = '</rtept>';
        }

        $xml[] = '</rte>';

        $xml[] = '</gpx>';

        return implode("\n", $xml);
    }


    public function getRouteGpx(string $item): string
    {
        $maxField = $this->maxFieldGenerator->getInfo($item);
        $wayPoints = $this->maxFieldGenerator->parseWayPointsFile($item);

        $steps = $this->calculateSteps($maxField->links);

        $xml = [];

        $xml[] = $this->getGpxHeader();

        $xml[] = '<rte>';
        $xml[] = '<name>Routenname</name>';

        foreach ($steps as $step) {
            $origin = $wayPoints[$step->origin];
            $xml[] = '<rtept lat="'.$origin->getLat().'"'
                .' lon="'.$origin->getLon().'">';
            $xml[] = '<name>'.$origin->getName().'</name>';
            $desc = implode(', ', $step->destinations);
            $xml[] = '<desc>'.$desc.'</desc>';
            $xml[] = '</rtept>';
        }

        $xml[] = '</rte>';

        $xml[] = '</gpx>';

        return implode("\n", $xml);
    }

    public function getTrackGpx(string $item): string
    {
        $maxField = $this->maxFieldGenerator->getInfo($item);
        $wayPoints = $this->maxFieldGenerator->parseWayPointsFile($item);

        $steps = $this->calculateSteps($maxField->links);

        $xml = [];

        $xml[] = $this->getGpxHeader();

        $xml[] = '<trk>';
        $xml[] = '<name>Routenname</name>';
        $xml[] = '<trkseg>';

        foreach ($steps as $step) {
            $origin = $wayPoints[$step->origin];
            $xml[] = '<trkpt lat="'.$origin->getLat().'"'
                .' lon="'.$origin->getLon().'">';
            $xml[] = '<name>'.$origin->getName().'</name>';
            $desc = implode(', ', $step->destinations);
            $xml[] = '<desc>'.$desc.'</desc>';
            $xml[] = '</trkpt>';
        }

        $xml[] = '</trkseg>';
        $xml[] = '</trk>';

        $xml[] = '</gpx>';

        return implode("\n", $xml);
    }

    /**
     * @param AgentLink[] $links
     */
    private function calculateSteps(array $links): array
    {
        $steps = [];
        $index = -1;
        $origin = '';
        foreach ($links as $link) {
            if ($link->originNum !== $origin) {
                $index++;
                $step = new \stdClass();
                $step->origin = $link->originNum;
                $step->destinations = [];

                $step->destinations[] = $link->destinationNum;

                $steps[$index] = $step;
                $origin = $link->originNum;
            } else {
                $steps[$index]->destinations[] = $link->destinationNum;
            }
        }

        return $steps;
    }

    private function getGpxHeader(): string
    {
        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<gpx version="1.0" creator="GPSBabel - http://www.gpsbabel.org"'
            .' xmlns="http://www.topografix.com/GPX/1/0">';
    }
}
