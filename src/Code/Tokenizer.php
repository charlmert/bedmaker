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

    private $mapClassNames = [];
    private $mapClassMethods = [];
    private $mapClassVariables = [];
    private $mapFunctions = [];
    private $mapVariables = [];

    public function __construct(string $source = null)
    {
        $this->source = $source;
        $this->modifiedSource = $source;
    }

    public function load(string $source) {
        $this->source = $source;
        $this->modifiedSource = $source;
    }

    public function setRules(array $rules) {
        $this->rules = $rules;
    }

    public function runAll() {
        $this->fixClassNames();
        $this->fixClassMethods();
        //$this->fixFunctions();
        $this->fixVariables();
        $this->fixClassNameUsage();
        $this->fixClassMethodUsage();
        $this->fixClassMemberUsage();
        $this->fixFunctionUsage();
        $this->fixVariableUsage();
        return $this->modifiedSource;
    }

    public function runAfter() {
        $this->fixClassNameUsage();
        $this->fixClassMethodUsage();
        $this->fixClassMemberUsage();
        //$this->fixFunctionUsage();
        $this->fixVariableUsage();
        return $this->modifiedSource;
    }

    private function checkSource() {
        if ($this->modifiedSource == '') {
            throw new \Exception('Please use load(string $source) to load php source');
        }
    }

    public function fixClassNames() {
        $this->checkSource();
        [$this->modifiedSource, $mapClassNames] = ClassNameRule::transform($this->modifiedSource);
        $this->mapClassNames = array_merge($this->mapClassNames, $mapClassNames);

        /*
        [$this->modifiedSource, $mapClassMethods] = ClassMethodRule::transform($this->modifiedSource);
        $this->mapClassMethods = array_merge($this->mapClassMethods, $mapClassMethods);
        [$this->modifiedSource, $mapClassVariables] = ClassMemberRule::transform($this->modifiedSource);
        $this->mapClassVariables = array_merge($this->mapClassVariables, $mapClassVariables);
        */
    }

    public function fixClassMethods() {
        $this->checkSource();
        [$this->modifiedSource, $mapClassMethods] = ClassMethodRule::transform($this->modifiedSource);
        $this->mapClassMethods = array_merge($this->mapClassMethods, $mapClassMethods);
    }

    public function fixFunctions() {
        $this->checkSource();
        [$this->modifiedSource, $mapFunctions] = FunctionRule::transform($this->modifiedSource);
        $this->mapFunctions = array_merge($this->mapFunctions, $mapFunctions);
    }

    public function fixVariables() {
        $this->checkSource();
        [$this->modifiedSource, $mapVariables] = VariableRule::transform($this->modifiedSource);
        $this->mapVariables = array_merge($this->mapVariables, $mapVariables);
    }

    public function fixClassNameUsage() {
        $this->checkSource();
        $this->modifiedSource = ClassNameRule::transformUsage($this->modifiedSource, $this->mapClassNames);
    }

    public function fixClassMethodUsage() {
        $this->checkSource();
        $this->modifiedSource = ClassMethodRule::transformUsage($this->modifiedSource, $this->mapClassMethods);
    }

    public function fixClassMemberUsage() {
        $this->checkSource();
        $this->modifiedSource = ClassMemberRule::transformUsage($this->modifiedSource, $this->mapClassVariables);
    }

    public function fixFunctionUsage() {
        $this->checkSource();
        $this->modifiedSource = FunctionRule::transformUsage($this->modifiedSource, $this->mapFunctions);
    }

    public function fixVariableUsage() {
        $this->checkSource();
        $this->modifiedSource = VariableRule::transformUsage($this->modifiedSource, $this->mapVariables);
    }
}
