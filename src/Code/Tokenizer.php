<?php

namespace BedMaker\Code;

use Funivan\PhpTokenizer\Collection;
use Funivan\PhpTokenizer\Pattern\PatternMatcher;
use Funivan\PhpTokenizer\QuerySequence\QuerySequence;

use BedMaker\Code\Style\CodeCase;

class Tokenizer
{
    private $source;

    public function __construct($source)
    {
        $this->source = $source;
    }

    public function runAll() {
        $retCode = '';
        $retCode .= $this->fixClasses();
        //$this->fixVariables();
        return $retCode;
    }

    public function fixClasses() {
        $collection = Collection::createFromString($this->source);

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) {
            $start = $q->strict('class');
            $space = $q->possible(T_WHITESPACE);
            $className = $q->possible(T_STRING);
            $extends = $q->possible(T_EXTENDS);
            $extendsClass = $q->possible(T_STRING);
            $implements = $q->possible(T_IMPLEMENTS);
            $implementsClass = $q->possible(T_STRING);
            $end = $q->search('{');

            if ($q->isValid()) {
                //$space->remove();
                //$start->setValue('class');
                //$end->prependToValue(')');
                if ($className->getValue() != null) {
                    $className->setValue(CodeCase::toStudly($className->getValue()));
                }

                if ($extendsClass->getValue() != null) {
                    $extendsClass->setValue(CodeCase::toStudly($extendsClass->getValue()));
                }

                if ($implementsClass->getValue() != null) {
                    $implementsClasses = explode(',', $implementsClass->getValue());
                    $tmpImplementsClass = [];

                    foreach ($implementsClasses as $class) {
                        $tmpImplementsClass[] = CodeCase::toStudly($class);
                    }

                    $implementsClass->setValue(join(',', $implementsClass));
                }
            }
        });

        return (string) $collection;
    }
}
