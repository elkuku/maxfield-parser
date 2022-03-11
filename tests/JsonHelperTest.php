<?php

use Elkuku\MaxfieldParser\JsonHelper;
use Elkuku\MaxfieldParser\MaxfieldParser;
use PHPUnit\Framework\TestCase;

class JsonHelperTest extends TestCase
{
    private string $testDir = __DIR__.'/testfiles';

    public function testGetJson(): void
    {
        $expected = trim(
            file_get_contents($this->testDir.'/test.json') ?: ''
        );

        $helper = new JsonHelper();

        $result = $helper->getJson(
            new MaxfieldParser($this->testDir.'/12345')
        );

        self::assertEquals($expected, $result);
    }
}
