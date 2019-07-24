<?php

namespace BedMaker\Code\Rule;

use Funivan\PhpTokenizer\Collection;
use Funivan\PhpTokenizer\Pattern\PatternMatcher;
use Funivan\PhpTokenizer\QuerySequence\QuerySequence;
use BedMaker\Code\Style\CodeCase;

class VariableRule
{
    const TRANSFORM_STUDLY = 'camel';

    public static function transform(string $source, $type = self::TRANSFORM_STUDLY) {
        $collection = Collection::createFromString($source);
        $mapVariables = [];

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) use ($type) {
            $variable = $q->strict(T_VARIABLE);
            $delim = $q->possible(T_WHITESPACE);
            $delim = $q->possible(')');
            $delim = $q->possible(']');
            $delim = $q->possible('\'');
            $delim = $q->possible('"');
            $end = $q->search(';');

            if ($q->isValid()) {
                if ($variable->getValue() != null) {
                    $newValue = CodeCase::toCamel($variable->getValue());

                    if ($type == 'camel') {
                        $newValue = CodeCase::toCamel($variable->getValue());
                    }

                    $mapVariables[$variable->getValue()] = $newValue;
                    $variable->setValue($newValue);
                }
            }
        });

        return [(string) $collection, $mapVariables];
    }

    public static function transformUsage(string $source, array $mapVariables) {
        $collection = Collection::createFromString($source);

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) use ($mapVariables) {
            $name = $q->strict(T_VARIABLE);
            $delim = $q->possible(T_WHITESPACE);
            $delim = $q->possible(')');
            $delim = $q->possible(']');
            $delim = $q->possible('\'');
            $delim = $q->possible('"');
            $end = $q->search(';');

            if ($q->isValid()) {
                if ($name->getValue() != null && isset($mapVariables[$name->getValue()])) {
                    $name->setValue($mapVariables[$name->getValue()]);
                }
            }
        });

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) use ($mapVariables) {
            $start = $q->strict(T_OBJECT_OPERATOR);
            $name = $q->strict(T_STRING);
            $end = $q->search(';');

            if ($q->isValid()) {
                if ($name->getValue() != null && isset($mapVariables[$name->getValue()])) {
                    $name->setValue($mapVariables[$name->getValue()]);
                }
            }
        });

        return (string) $collection;
    }
}
