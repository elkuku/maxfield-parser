<?php

use Elkuku\MaxfieldParser\MaxfieldParser;

require_once __DIR__ . '/../vendor/autoload.php';

$parser = new MaxfieldParser();

echo $parser->hello();
