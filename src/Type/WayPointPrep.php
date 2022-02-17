<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 16.10.18
 * Time: 10:23
 */

namespace Elkuku\MaxfieldParser\Type;

class WayPointPrep
{
    public function __construct(
        public int $agentNum = 0,
        public int $mapNo = 0,
        public string $name = '',
        public int $keysNeeded = 0,
    ) {
    }
}
