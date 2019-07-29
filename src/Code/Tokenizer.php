<?php

namespace BedMaker\Code;

use BedMaker\Config;
use BedMaker\Code\Rule\ClassNamespaceRule;
use BedMaker\Code\Rule\ClassNameRule;
use BedMaker\Code\Rule\ClassMethodRule;
use BedMaker\Code\Rule\ClassMemberRule;
use BedMaker\Code\Rule\CommentRule;
use BedMaker\Code\Rule\FunctionRule;
use BedMaker\Code\Rule\VariableRule;

class Tokenizer
{
    private $config;
    private $source;
    private $modifiedSource;

    private $mapClassNamespaces = [];
    private $mapClassNames = [];
    private $mapClassMethods = [];
    private $mapClassVariables = [];
    private $mapFunctions = [];
    private $mapVariables = [];

    public function __construct(string $source = null, Config $config = null)
    {
        $this->source = $source;
        $this->modifiedSource = $source;
        $this->config = $config;
    }

    public function load(string $source, Config $config = null) {
        $this->source = $source;
        $this->modifiedSource = $source;
        if ($config != null) {
            $this->config = $config;
        }
    }

    public function setRules(array $rules) {
        $this->rules = $rules;
    }

    public function runAll() {
        $this->fixClassNamespaces();
        $this->fixClassNames();
        dd('here');
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
        //$this->fixClassNamespaceUsage();
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

    public function fixClassNamespaces() {
        $this->checkSource();
        [$this->modifiedSource, $mapClassNamespaces] = ClassNamespaceRule::transform($this->modifiedSource, $this->config);
        $this->mapClassNamespaces = array_merge($this->mapClassNamespaces, $mapClassNamespaces);
    }

    public function fixClassNames() {
        $this->checkSource();
        [$this->modifiedSource, $mapClassNames] = ClassNameRule::transform($this->modifiedSource);
        $this->mapClassNames = array_merge($this->mapClassNames, $mapClassNames);
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
