<?php

namespace BedMaker\Tests;

use PHPUnit\Framework\TestCase;

class CodeCaseTest extends TestCase
{
    /**
     * @return void
     */
    public function test()
    {
        $this->assertEquals(1, $this->card->resultCode);
    }
}
