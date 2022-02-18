<?php
/**
 * Created by PhpStorm.
 * User: elkuku
 * Date: 11.10.18
 * Time: 14:14
 */

namespace Elkuku\MaxfieldParser\Type;

class MaxField
{
    public KeyPrep $keyPrep;
    public string $keyPrepTxt = '';
    public string $ownershipPrep = '';

    /**
     * @var AgentInfo[]
     */
    public array $agentsInfo = [];

    public int $frames = 0;

    /**
     * @var AgentLink[]
     */
    public array $links;

    /**
     * @var Step[]
     */
    public array $steps;
}
