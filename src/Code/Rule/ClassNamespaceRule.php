<?php

namespace BedMaker\Code\Rule;

use Funivan\PhpTokenizer\Collection;
use Funivan\PhpTokenizer\Pattern\PatternMatcher;
use Funivan\PhpTokenizer\QuerySequence\QuerySequence;
use BedMaker\Code\Style\CodeCase;
use BedMaker\Config;
use BedMaker\Str\Rename;

class ClassNamespaceRule
{
    public static function transform(string $source, Config $config = null) {
        preg_match_all('/namespace.*;/', $source, $matches);
        if ($config) {
            $namespace = $config->get('rules.class.namespace', '');
            if ($namespace != '') {
                if ((isset($matches[0]) && empty($matches[0]))) {
                    $codeParts = preg_split('/<\?php/i', $source);
                    if (count($codeParts) > 1) {
                        $returnSource = '<?php' . "\n\n";
                        $returnSource .= 'namespace ' . $namespace . ";\n";
                        $returnSource .= $codeParts[1];
                        $mapNamespaces = [
                            $namespace
                        ];
                        dd($returnSource);
                        return [(string) $returnSource, $mapNamespaces];
                    } else {
                        throw new \Exception('missing <?php open tag in source');
                    }
                }
            }
        }

        return [(string) $source, []];
    }

    public static function transformWithTokenizer(string $source, Config $config = null) {
        $collection = Collection::createFromString($source);
        $mapClasses = [];

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) use ($type, &$mapClasses) {
            $start = $q->strict(T_NAMESPACE);
            $space = $q->possible(T_WHITESPACE);
            $namespace = $q->possible(T_STRING);
            //$namespace = $q->groupRepeat(T_STRING|T_NS_SEPARATOR)
            //@TODO: create group and groupRepeat functions to match portions of code
            // until delim or to match repeating portions of code until delim.
            $end = $q->search(';');

            if ($q->isValid()) {

            }
        });

        return [(string) $collection, $mapClasses];
    }

    /**
     * @TODO: The usage function needs to be used on larger projects where the
     * calling code is in another project or directory for e.g.
     *
     */
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
