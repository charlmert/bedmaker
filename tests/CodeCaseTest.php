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
        $source = <<<'EOF'
            $db_man;
            "$db_man";
            ($db_man);
            [$db_man];
            '$db_man';
            $db_man = 'hello';
EOF;
        $this->assertEquals(1, $this->card->resultCode);
    }
}
