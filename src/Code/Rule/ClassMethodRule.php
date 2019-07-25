<?php

namespace BedMaker\Code\Rule;

use Funivan\PhpTokenizer\Collection;
use Funivan\PhpTokenizer\Pattern\PatternMatcher;
use Funivan\PhpTokenizer\QuerySequence\QuerySequence;
use BedMaker\Code\Style\CodeCase;

class ClassMethodRule
{
    const TRANSFORM_STUDLY = 'studly';
    const PHP_MAGIC_METHODS = [
      '__construct',
      '__destruct',
      '__call',
      '__callStatic',
      '__get',
      '__set',
      '__isset',
      '__unset',
      '__sleep',
      '__wakeup',
      '__toString',
      '__invoke',
      '__set_state',
      '__clone',
      '__debugInfo'
    ];

    public static function transform(string $source, $type = self::TRANSFORM_STUDLY) {
        $collection = Collection::createFromString($source);
        $mapMethods = [];

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) use ($type, &$mapMethods) {
            $start = $q->strict('function');
            $space = $q->possible(T_WHITESPACE);
            $name = $q->possible(T_STRING);
            $end = $q->search('{');

            if ($q->isValid()) {
                if ($name->getValue() != null && !in_array($name->getValue(), self::PHP_MAGIC_METHODS)) {
                    $newValue = CodeCase::toCamel($name->getValue());

                    if ($type == 'camel') {
                        $newValue = CodeCase::toCamel($name->getValue());
                    }

                    $mapMethods[$name->getValue()] = $newValue;
                    $name->setValue($newValue);
                }
            }
        });

        return [(string) $collection, $mapMethods];
    }

    public static function transformUsage(string $source, array $mapMethods) {
        $collection = Collection::createFromString($source);

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) use ($mapMethods) {
            $name = $q->strict(T_STRING);
            $delim = $q->possible(T_WHITESPACE);
            $delim = $q->possible('(');
            $delim = $q->possible('\'');
            $delim = $q->possible('"');
            $end = $q->search('(');

            if ($q->isValid()) {
                if ($name->getValue() != null && isset($mapMethods[$name->getValue()])) {
                    $name->setValue($mapMethods[$name->getValue()]);
                }
            }
        });

        return (string) $collection;
    }
}
