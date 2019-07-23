<?php

namespace BedMaker\Code\Style;

use Illuminate\Support\Str;

class CodeCase
{
    /**
     * Converts to camel case.
     *
     * @param string $str The string to convert.
     *
     * @return string
     */
    public static function toCamel(string $str)
    {
        return Str::camel($str);
    }

    /**
     * Converts to camel case.
     *
     * @param string $str The string to convert.
     *
     * @return string
     */
    public static function toStudly(string $str)
    {
        return Str::studly($str);
    }
}
