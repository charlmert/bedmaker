<?php

namespace BedMaker\Str;

use Illuminate\Support\Str;

class Rename
{
    public static function transform($subject, $search = '', $replaceString = '') {
        if (Str::startsWith($search, '/') && !Str::startsWith($search, '\/')) {
            return self::transformRegex($subject, $search, $replaceString);
        } else {
            return self::transformString($subject, $search, $replaceString);
        }
    }

    public static function transformString($subject, $searchString = '', $replaceString = '') {
        return str_replace($searchString, $replaceString, $subject);
    }

    public static function transformRegex($subject, $searchPattern = '//', $replaceString = '') {
        return preg_replace($searchPattern, $replaceString, $subject);
    }
}
