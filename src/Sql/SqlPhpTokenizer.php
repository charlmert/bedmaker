<?php

namespace BedMaker\Sql;

use BedMaker\Sql\Tokenizer;
use Funivan\PhpTokenizer\Collection;
use Funivan\PhpTokenizer\Pattern\PatternMatcher;
use Funivan\PhpTokenizer\QuerySequence\QuerySequence;
use BedMaker\Code\Style\CodeCase;
use BedMaker\Log\Logger;

class SqlPhpTokenizer
{
    private $source;
    private $modifiedSource;

    public function __construct(string $source = null)
    {
        $this->source = $source;
        $this->modifiedSource = $source;
    }

    public function load(string $source) {
        $this->source = $source;
        $this->modifiedSource = $source;
    }

    public function process() {
        $collection = Collection::createFromString($this->modifiedSource);
        $mapVariables = [];

        (new PatternMatcher($collection))->apply(function (QuerySequence $q) {
            $query = $q->strict(T_ENCAPSED_AND_WHITESPACE);
            $end = $q->search(';');

            if ($q->isValid()) {
                if ($query->getValue() != null) {
                    try {
                        $tokenizer = new Tokenizer($query->getValue());
                        $newValue = $tokenizer->toElloquent();
                        $query->setValue($newValue);
                    } catch (\Exception $e) {
                        dump($query->getValue());
                        dd($e);
                        //Logger::error($e);
                    }
                }
            }
        });

        $this->modifiedSource = (string) $collection;
        return $this->modifiedSource;
    }
}
