<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 16.10.18
 * Time: 10:22
 */

namespace Elkuku\MaxfieldParser\Type;

class KeyPrep
{
    /**
     * @var WayPointPrep[]
     */
    private array $wayPoints = [];

    public function addWayPoint(WayPointPrep $wayPoint): self
    {
        $this->wayPoints[] = $wayPoint;

        usort(
            $this->wayPoints,
            static function ($a, $b) {
                return $a->mapNo - $b->mapNo;
            }
        );

        return $this;
    }

    /**
     * @return WayPointPrep[]
     */
    public function getWayPoints(): array
    {
        return $this->wayPoints;
    }
}
