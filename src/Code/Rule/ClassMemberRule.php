<?php

namespace BedMaker\Code\Rule;

use Funivan\PhpTokenizer\Collection;
use Funivan\PhpTokenizer\Pattern\PatternMatcher;
use Funivan\PhpTokenizer\QuerySequence\QuerySequence;
use BedMaker\Code\Style\CodeCase;

class ClassMemberRule
{
    const TRANSFORM_STUDLY = 'studly';

    public static function transform(string $source, $type = self::TRANSFORM_STUDLY) {
        return ['', []];
    }

    public static function transformUsage(string $source, array $mapMethods) {
        return (string) $source;
    }
}
