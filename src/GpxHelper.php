<?php

namespace Elkuku\MaxfieldParser;

use Elkuku\MaxfieldParser\Type\KeyPrep;
use Elkuku\MaxfieldParser\Type\LinkStep;
use Elkuku\MaxfieldParser\Type\Waypoint;

class GpxHelper
{
    public function getWaypointsGpx(MaxfieldParser $maxfieldParser): string
    {
        $keyPrep = $maxfieldParser->getKeyPrep();
        $wayPoints = $maxfieldParser->getWayPoints();

        $xml = array_merge(
            $this->getPartHeader(),
            $this->getPartWaypoints($keyPrep, $wayPoints),
            $this->getPartFooter(),
        );

        return implode("\n", $xml);
    }

    public function getRouteTrackGpx(MaxfieldParser $maxfieldParser): string
    {
        $keyPrep = $maxfieldParser->getKeyPrep();
        $wayPoints = $maxfieldParser->getWayPoints();
        $links = $maxfieldParser->getLinkSteps();

        $xml = array_merge(
            $this->getPartHeader(),
            $this->getPartWaypoints($keyPrep, $wayPoints),
            $this->getPartRoute($links, $wayPoints),
            $this->getPartFooter(),
        );

        return implode("\n", $xml);
    }

    public function getRouteGpx(MaxfieldParser $maxfieldParser): string
    {
        $wayPoints = $maxfieldParser->getWayPoints();
        $links = $maxfieldParser->getLinkSteps();

        $xml = array_merge(
            $this->getPartHeader(),
            $this->getPartRoute($links, $wayPoints),
            $this->getPartFooter(),
        );

        return implode("\n", $xml);
    }

    public function getTrackGpx(MaxfieldParser $maxfieldParser): string
    {
        $wayPoints = $maxfieldParser->getWayPoints();
        $links = $maxfieldParser->getLinkSteps();

        $xml = array_merge(
            $this->getPartHeader(),
            $this->getPartTrack($links, $wayPoints),
            $this->getPartFooter(),
        );

        return implode("\n", $xml);
    }

    /**
     * @return string[]
     */
    private function getPartHeader(): array
    {
        return [
            '<?xml version="1.0" encoding="UTF-8"?>',
            '<gpx version="1.0" creator="GPSBabel - http://www.gpsbabel.org" xmlns="http://www.topografix.com/GPX/1/0">',
        ];
    }

    /**
     * @return string[]
     */
    private function getPartFooter(): array
    {
        return ['</gpx>'];
    }

    /**
     * @param LinkStep[] $steps
     * @param Waypoint[] $wayPoints
     *
     * @return string[]
     */
    private function getPartTrack(array $steps, array $wayPoints): array
    {
        $xml = [];

        $xml[] = '<trk>';
        $xml[] = '<name>Track name</name>';
        $xml[] = '<trkseg>';

        foreach ($steps as $step) {
            $origin = $wayPoints[$step->origin];
            $xml[] = '<trkpt lat="'.$origin->lat.'"'
                .' lon="'.$origin->lon.'">';
            $xml[] = '<name>'.$origin->name.'</name>';
            $desc = implode(', ', $step->getDestinations());
            $xml[] = '<desc>'.$desc.'</desc>';
            $xml[] = '</trkpt>';
        }

        $xml[] = '</trkseg>';
        $xml[] = '</trk>';

        return $xml;
    }

    /**
     * @param Waypoint[] $wayPoints
     *
     * @return string[]
     */
    private function getPartWaypoints(KeyPrep $keyPrep, array $wayPoints): array
    {
        $xml = [];

        foreach ($keyPrep->getWayPoints() as $wayPointAgent) {
            $wayPoint = $wayPoints[$wayPointAgent->mapNo];
            $xml[] = '<wpt lat="'.$wayPoint->lat.'" lon="'
                .$wayPoint->lon.'">';
            $xml[] = '  <name>'.$wayPoint->name.'</name>';
            $xml[] = '  <desc>Farm keys: '.$wayPointAgent->keysNeeded.'</desc>';
            $xml[] = '</wpt>';
        }

        return $xml;
    }

    /**
     * @param LinkStep[] $steps
     * @param Waypoint[] $waypoints
     *
     * @return string[]
     */
    private function getPartRoute(array $steps, array $waypoints): array
    {
        $xml = [];

        $xml[] = '<rte>';
        $xml[] = '<name>Route name</name>';

        foreach ($steps as $step) {
            $origin = $waypoints[$step->origin];
            $xml[] = '<rtept lat="'.$origin->lat.'"'
                .' lon="'.$origin->lon.'">';
            $xml[] = '<name>'.$origin->name.'</name>';
            $linksString = '';
            foreach ($step->getDestinations() as $index) {
                $linksString .= 'Link: '.$waypoints[$index]->name.'*BR*';
            }

            $xml[] = '<desc>'.$linksString.'</desc>';
            $xml[] = '</rtept>';
        }

        $xml[] = '</rte>';

        return $xml;
    }
}
