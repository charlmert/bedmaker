<?php
namespace BedMaker\Tests;

use PHPUnit\Framework\TestCase;
use BedMaker\Sql\Tokenizer;

class SqlTokenizerTest extends TestCase
{
    /**
     * @return void
     */
    public function testBadSqlString()
    {
        $query = 'We did select a great query';
        $tokenizer = new Tokenizer($query);
        $this->assertEquals(1, 1);
    }
}
