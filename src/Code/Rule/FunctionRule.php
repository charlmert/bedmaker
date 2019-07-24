<?php

namespace BedMaker\Code\Rule;

use Funivan\PhpTokenizer\Collection;
use Funivan\PhpTokenizer\Pattern\PatternMatcher;
use Funivan\PhpTokenizer\QuerySequence\QuerySequence;
use BedMaker\Code\Style\CodeCase;

class FunctionRule
{
    const TRANSFORM_STUDLY = 'camel';

    public static function transform(string $source, $type = self::TRANSFORM_STUDLY) {
        $collection = Collection::createFromString($source);
        $mapFunctions = [];

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) use ($type) {
            $start = $q->strict('function');
            $space = $q->possible(T_WHITESPACE);
            $name = $q->possible(T_STRING);
            $end = $q->search('{');

            if ($q->isValid()) {
                if ($name->getValue() != null) {
                    $newValue = CodeCase::toCamel($name->getValue());

                    if ($type == 'camel') {
                        $newValue = CodeCase::toCamel($name->getValue());
                    }

                    $mapFunctions[$name->getValue()] = $newValue;
                    $name->setValue($newValue);
                }
            }
        });

        return [(string) $collection, $mapFunctions];
    }

    public static function transformUsage(string $source, array $mapFunctions) {
        $collection = Collection::createFromString($source);

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) use ($mapFunctions) {
            $start = $q->possible(T_FUNCTION);
            $name = $q->possible(T_STRING);
            $end = $q->search('(');

            if ($q->isValid()) {
                if ($name->getValue() != null && isset($mapFunctions[$name->getValue()])) {
                    $name->setValue($mapFunctions[$name->getValue()]);
                }
            }
        });

        return (string) $collection;
    }
}
