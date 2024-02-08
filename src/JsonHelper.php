<?php

namespace Elkuku\MaxfieldParser;

use Elkuku\MaxfieldParser\Type\KeyPrep;
use Elkuku\MaxfieldParser\Type\LinkStep;
use Elkuku\MaxfieldParser\Type\Waypoint;

class JsonHelper
{
    /**
     * @return array<string, array<Waypoint|\stdClass>>
     */
    public function getJsonData(MaxfieldParser $maxfieldParser): array
    {
        $wayPoints = $maxfieldParser->getWayPoints();

        return [
            'waypoints' => $this->getPartWaypoints(
                $maxfieldParser->getKeyPrep(),
                $wayPoints
            ),
            'links'     => $this->getPartLinks(
                $maxfieldParser->getLinkSteps(),
                $wayPoints
            ),
        ];
    }

    /**
     * @throws \JsonException
     */
    public function getJson(MaxfieldParser $maxfieldParser): string
    {

        return json_encode($this->getJsonData($maxfieldParser), JSON_THROW_ON_ERROR);
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
                keys: $wayPointAgent->keysNeeded,
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
                $l = new \stdClass();

                $l->num = $index;
                $l->name = str_replace('\'', '', $waypoints[$index]->name);

                $link->links[] = $l;
            }

            $links[] = $link;
        }

        return $links;
    }
}
