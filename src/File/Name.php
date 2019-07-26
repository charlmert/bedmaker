<?php

namespace BedMaker\File;


use Funivan\PhpTokenizer\Collection;
use Funivan\PhpTokenizer\Pattern\PatternMatcher;
use Funivan\PhpTokenizer\QuerySequence\QuerySequence;
use BedMaker\Str\Rename;
use Illuminate\Support\Str;
use BedMaker\Config;
use BedMaker\Code\Rule\ClassNameRule;

class Name
{
    public static function transform($filename, Config $config = null, $source = '', $extention = '.php') {
        $returnFilename = $filename;

        if ($config) {
            $case = $config->get('rules.file.name.case');
            if ($case === 'studly') {
                $returnFilename = Str::studly($filename);
            }

            $renameFrom = $config->get('rules.file.rename.from', '');
            $renameTo = $config->get('rules.file.rename.to', '');

            if ($renameFrom != '' && $renameTo != '') {
                $returnFilename = Rename::transform($returnFilename, $renameFrom, $renameTo);
            }

            $rename = $config->get('rules.file.rename', '');
            if ($rename === 'class' && $source != '') {
                $returnFilename = ClassNameRule::getClassName($source) . $extention;
            }

            return $returnFilename;
        }

        return $filename;
    }
}
