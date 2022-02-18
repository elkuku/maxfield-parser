<?php

namespace Elkuku\MaxfieldParser\Type;

class LinkStep
{
    private array $destinations = [];

    public function __construct(
        public int $origin
    ) {
    }

    public function getDestinations(): array
    {
        return $this->destinations;
    }

    public function addtDestination(int $destinations): self
    {
        $this->destinations[] = $destinations;

        return $this;
    }

}
