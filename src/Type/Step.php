<?php

namespace Elkuku\MaxfieldParser\Type;

class Step
{
    public const TYPE_LINK = 1;
    public const TYPE_MOVE = 2;

    public function __construct(
        public int $action = 0,
        public int $linkNum = 0,
        public int $agentNum = 0,
        public int $originNum = 0,
        public string $originName = '',
        public int $destinationNum = 0,
        public string $destinationName = '',
    ) {
    }
}
