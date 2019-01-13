<?php
/**
 * Created by PhpStorm.
 * User: mk990
 * Date: 1/13/2019
 * Time: 12:09 AM
 */

namespace MkOrm\Utils;


class Utils
{
    public static function deCamelize($word)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $word));
    }

    public static function camelize($word)
    {
        $words = explode('_', $word);
        $words = array_map('ucfirst', $words);
        return lcfirst(str_replace('_', '', implode('_', $words)));
    }
}
