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

    private $mapClass = [];
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
        $this->fixClasses();
        return $this->modifiedSource;
    }

    public function fixClasses() {
        $this->modifiedSource = ClassNameRule::transform($this->modifiedSource);
        $this->modifiedSource = ClassMethodRule::transform($this->modifiedSource);
        $this->modifiedSource = ClassMemberRule::transform($this->modifiedSource);

        $this->fixFunctions();
        $this->fixVariables();
        $this->fixClassUsage();
        $this->fixFunctionUsage();
        $this->fixVariableUsage();
        return $this->modifiedSource;
    }

    public function runAfter() {
        $this->fixClassUsage();
        $this->fixFunctionUsage();
        $this->fixVariableUsage();
        return $this->modifiedSource;
    }

    private function checkSource() {
        if ($this->modifiedSource == '') {
            throw new Exception('Please use load(string $source) to load php source');
        }
    }

    public function fixClasses() {
        $this->checkSource();
        $collection = Collection::createFromString($this->modifiedSource);

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) {
            $start = $q->strict('class');
            $space = $q->possible(T_WHITESPACE);
            $className = $q->possible(T_STRING);
            $space2 = $q->possible(T_WHITESPACE);
            $extends = $q->possible(T_EXTENDS);
            $space3 = $q->possible(T_WHITESPACE);
            $extendsClass = $q->possible(T_STRING);
            $space3 = $q->possible(T_WHITESPACE);
            $implements = $q->possible(T_IMPLEMENTS);
            $space3 = $q->possible(T_WHITESPACE);
            $implementsClass = $q->possible(T_STRING);

            //@TODO: make QuerySequence parse remaining implements classes
            // else will have to pass T_IMPLEMENTS, T_WHITESPACE, T_STRING, T_WHITESPACE etc.

            $end = $q->search('{');

            if ($q->isValid()) {

                // possible token operations:
                //$space->remove();
                //$start->setValue('class');
                //$end->prependToValue(')');

                if ($className->getValue() != null) {
                    $newValue = CodeCase::toStudly($className->getValue());
                    $this->mapClasses[$className->getValue()] = $newValue;
                    $className->setValue($newValue);
                }

                if ($extendsClass->getValue() != null) {
                    $newValue = CodeCase::toStudly($extendsClass->getValue());
                    $this->mapClasses[$extendsClass->getValue()] = $newValue;
                    $extendsClass->setValue($newValue);
                }

                  if ($implementsClass->getValue() != null) {
                    $implementsClasses = explode(',', $implementsClass->getValue());
                    $tmpImplementsClass = [];

                    foreach ($implementsClasses as $class) {
                        $newValue = CodeCase::toStudly($class);
                        $this->mapClasses[$class] = $newValue;
                        $tmpImplementsClass[] = $newValue;
                    }

                    $implementsClass->setValue(join(',', $implementsClass));
                }
            }
        });

        $this->modifiedSource = (string) $collection;
    }

    public function fixFunctions() {
        $this->checkSource();
        $collection = Collection::createFromString($this->modifiedSource);

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) {
            $start = $q->strict('function');
            $space = $q->possible(T_WHITESPACE);
            $name = $q->possible(T_STRING);
            $end = $q->search('{');

            if ($q->isValid()) {
                if ($name->getValue() != null) {
                    $newValue = CodeCase::toCamel($name->getValue());
                    $this->mapFunctions[$name->getValue()] = $newValue;
                    $name->setValue($newValue);
                }
            }
        });

        $this->modifiedSource = (string) $collection;
    }

    public function fixVariables() {
        $this->checkSource();
        $collection = Collection::createFromString($this->modifiedSource);

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) {
            $variable = $q->strict(T_VARIABLE);
            $end = $q->search(';');

            if ($q->isValid()) {
                if ($variable->getValue() != null) {
                    $newValue = CodeCase::toCamel($variable->getValue());
                    $this->mapVariables[$variable->getValue()] = $newValue;
                    $variable->setValue($newValue);
                }
            }
        });

        $this->modifiedSource = (string) $collection;
    }

    public function fixClassUsage() {
        $this->checkSource();
        $collection = Collection::createFromString($this->modifiedSource);

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) {
            $start = $q->strict('new');
            $space = $q->possible(T_WHITESPACE);
            $name = $q->possible(T_STRING);
            $end = $q->search('{');

            if ($q->isValid()) {
                if ($name->getValue() != null && isset($this->mapClasses[$name->getValue()])) {
                    $name->setValue($this->mapClasses[$name->getValue()]);
                }
            }
        });

        $this->modifiedSource = (string) $collection;
    }

    public function fixFunctionUsage() {
        $this->checkSource();
        $collection = Collection::createFromString($this->modifiedSource);

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) {
            $start = $q->possible(T_FUNCTION);
            $name = $q->possible(T_STRING);
            $end = $q->search('(');

            if ($q->isValid()) {
                if ($name->getValue() != null && isset($this->mapFunctions[$name->getValue()])) {
                    $name->setValue($this->mapFunctions[$name->getValue()]);
                }
            }
        });

        $this->modifiedSource = (string) $collection;
    }

    public function fixVariableUsage() {
        $this->checkSource();
        $collection = Collection::createFromString($this->modifiedSource);

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) {
            $name = $q->strict(T_VARIABLE);
            $delim = $q->possible(T_WHITESPACE);
            $delim = $q->possible(')');
            $delim = $q->possible(']');
            $end = $q->search(';');

            if ($q->isValid()) {
                if ($name->getValue() != null && isset($this->mapVariables[$name->getValue()])) {
                    $name->setValue($this->mapVariables[$name->getValue()]);
                }
            }
        });

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) {
            $start = $q->strict(T_OBJECT_OPERATOR);
            $name = $q->strict(T_STRING);
            $end = $q->search(';');

            if ($q->isValid()) {
                if ($name->getValue() != null && isset($this->mapVariables[$name->getValue()])) {
                    $name->setValue($this->mapVariables[$name->getValue()]);
                }
            }
        });

        $this->modifiedSource = (string) $collection;
    }
}
