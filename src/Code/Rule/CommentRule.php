<?php

namespace BedMaker\Code\Rule;

use Funivan\PhpTokenizer\Collection;
use Funivan\PhpTokenizer\Pattern\PatternMatcher;
use Funivan\PhpTokenizer\QuerySequence\QuerySequence;
use BedMaker\Code\Style\CodeCase;

class CommentRule
{
    const TRANSFORM_STUDLY = 'studly';

    public static function transform(string $source, $type = self::TRANSFORM_STUDLY) {
        return ['', []];
    }
}
