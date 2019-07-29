<?php

namespace BedMaker\Code\Rule;

use Funivan\PhpTokenizer\Collection;
use Funivan\PhpTokenizer\Pattern\PatternMatcher;
use Funivan\PhpTokenizer\QuerySequence\QuerySequence;
use BedMaker\Code\Style\CodeCase;
use BedMaker\Config;
use BedMaker\Str\Rename;

class ClassNameRule
{
    const TRANSFORM_STUDLY = 'studly';

    public static function transform(string $source, $type = self::TRANSFORM_STUDLY, Config $config = null) {
        $mapClasses = [];
        preg_match_all('/class.*/s', $source, $matches);
        if (isset($matches[0])) {
            dd($matches[0]);
        }
    }

    public static function transformWithTokenizer(string $source, $type = self::TRANSFORM_STUDLY, Config $config = null) {
        $collection = Collection::createFromString($source);
        $mapClasses = [];

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) use ($type, &$mapClasses) {
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
                    $mapClasses[$className->getValue()] = $newValue;
                    $className->setValue($newValue);
                }

                if ($extendsClass->getValue() != null) {
                    $newValue = CodeCase::toStudly($extendsClass->getValue());
                    $mapClasses[$extendsClass->getValue()] = $newValue;
                    $extendsClass->setValue($newValue);
                }

                if ($implementsClass->getValue() != null) {
                    $implementsClasses = explode(',', $implementsClass->getValue());
                    $tmpImplementsClass = [];

                    foreach ($implementsClasses as $class) {
                        $newValue = CodeCase::toStudly($class);

                        if ($config->get('rules.class.name.rename.from', '') != '') {
                            $newValue = Rename::transform($newValue, $config->get('rules.class.name.rename.from', ''), $config->get('rules.class.name.rename.to', ''));
                        }

                        $mapClasses[$class] = $newValue;
                        $tmpImplementsClass[] = $newValue;
                    }

                    $implementsClass->setValue(join(',', $implementsClass));
                }
            }
        });

        return [(string) $collection, $mapClasses];
    }

    public static function transformUsage(string $source, array $mapClassNames) {
        $collection = Collection::createFromString($source);

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) use ($mapClassNames) {
            $start = $q->strict('new');
            $space = $q->possible(T_WHITESPACE);
            $name = $q->possible(T_STRING);
            $end = $q->search('(');

            if ($q->isValid()) {
                if ($name->getValue() != null && isset($mapClassNames[$name->getValue()])) {
                    $name->setValue($mapClassNames[$name->getValue()]);
                }
            }
        });

        return (string) $collection;
    }

    public static function getClassName($source, $default = '') {
        $collection = Collection::createFromString($source);
        $mapClasses = [];
        $returnFilename = '';

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) use (&$returnFilename) {
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
            $end = $q->search('{');

            if ($q->isValid()) {
                $returnFilename = $className->getValue();
            }
        });

        if ($returnFilename != '') {
            return $returnFilename;
        }

        return $default;
    }
}
