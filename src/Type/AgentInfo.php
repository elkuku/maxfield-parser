<?php

namespace Elkuku\MaxfieldParser\Type;

class AgentInfo
{
    public int $agentNumber = 0;

    public string $keysInfo = '';
    public string $linksInfo = '';

    /**
     * @var AgentLink[]
     */
    public array $links = [];
    public KeyPrep $keys;
}
