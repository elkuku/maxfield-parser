<?php

namespace Elkuku\MaxfieldParser\Type;

class LinkStep
{
    /**
     * @var int[]
     */
    private array $destinations = [];

    public function __construct(
        public int $origin
    ) {
    }

    /**
     * @return int[]
     */
    public function getDestinations(): array
    {
        return $this->destinations;
    }

    public function addDestination(int $destinations): self
    {
        $this->destinations[] = $destinations;

        return $this;
    }

}
