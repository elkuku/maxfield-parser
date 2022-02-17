<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 16.10.18
 * Time: 12:19
 */

namespace Elkuku\MaxfieldParser\Type;

class AgentLink
{
    public function __construct(
        public int $linkNum = 0,
        public int $agentNum = 0,

        public bool $isEarly = false,

        public int $originNum = 0,
        public string $originName = '',
        public int $destinationNum = 0,
        public string $destinationName = '',
    ) {
    }
}
