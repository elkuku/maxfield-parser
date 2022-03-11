<?php

namespace Elkuku\MaxfieldParser;

use Elkuku\MaxfieldParser\Type\KeyPrep;
use Elkuku\MaxfieldParser\Type\LinkStep;
use Elkuku\MaxfieldParser\Type\Waypoint;

class JsonHelper
{
    /**
     * @throws \JsonException
     */
    public function getJson(MaxfieldParser $maxfieldParser): string
    {
        $wayPoints = $maxfieldParser->getWayPoints();

        $result = [
            'waypoints' => $this->getPartWaypoints(
                $maxfieldParser->getKeyPrep(),
                $wayPoints
            ),
            'links'     => $this->getPartLinks(
                $maxfieldParser->getLinkSteps(),
                $wayPoints
            ),
        ];

        return json_encode($result, JSON_THROW_ON_ERROR);
    }

    /**
     * @param Waypoint[] $wayPoints
     *
     * @return Waypoint[]
     */
    private function getPartWaypoints(KeyPrep $keyPrep, array $wayPoints): array
    {
        $points = [];

        foreach ($keyPrep->getWayPoints() as $wayPointAgent) {
            $wayPoint = $wayPoints[$wayPointAgent->mapNo];

            $name = str_replace('\'', '', $wayPoint->name);

            $points[] = new Waypoint(
                name: $name,
                lat: $wayPoint->lat,
                lon: $wayPoint->lon,
                description: 'Farm keys: '.$wayPointAgent->keysNeeded
            );
        }

        return $points;
    }

    /**
     * @param LinkStep[] $steps
     * @param Waypoint[] $waypoints
     *
     * @return \stdClass[]
     */
    private function getPartLinks(array $steps, array $waypoints): array
    {
        $links = [];

        foreach ($steps as $step) {
            $origin = $waypoints[$step->origin];

            $link = new \stdClass();

            $name = str_replace('\'', '', $origin->name);

            $link->lat = $origin->lat;
            $link->lon = $origin->lon;
            $link->name = $name;
            $link->links = [];

            foreach ($step->getDestinations() as $index) {
                $name = str_replace('\'', '', $waypoints[$index]->name);
                $link->links[] = $name;
            }

            $links[] = $link;
        }

        return $links;
    }
}
