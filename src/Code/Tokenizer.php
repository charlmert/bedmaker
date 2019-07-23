<?php

namespace BedMaker\Code;

use BedMaker\Code\Rule\ClassNameRule;
use BedMaker\Code\Rule\ClassMethodRule;
use BedMaker\Code\Rule\ClassMemberRule;
use BedMaker\Code\Rule\CommentRule;
use BedMaker\Code\Rule\FunctionRule;
use BedMaker\Code\Rule\VariableRule;

class Tokenizer
{
    private $source;
    private $modifiedSource;

    public function __construct($source)
    {
        $this->source = $source;
        $this->modifiedSource = $source;
    }

    public function runAll() {
        $this->fixClasses();
        return $this->modifiedSource;
    }

    public function fixClasses() {
        $this->modifiedSource = ClassNameRule::transform($this->modifiedSource);
        $this->modifiedSource = ClassMethodRule::transform($this->modifiedSource);
        $this->modifiedSource = ClassMemberRule::transform($this->modifiedSource);
    }
}
