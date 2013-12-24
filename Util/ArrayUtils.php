<?php

namespace ITE\FormBundle\Util;

/**
 * Class ArrayUtils
 * @package ITE\FormBundle\Util
 */
class ArrayUtils
{
    /**
     * @return mixed
     */
    public static function replaceRecursive()
    {
        $arrays = func_get_args();
        $base = array_shift($arrays);

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (isset($base[$key]) && is_array($base[$key]) && is_array($value) && static::isAssociative($value)) {
                    $base[$key] = static::replaceRecursive($base[$key], $value);
                } else {
                    $base[$key] = $value;
                }
            }
        }

        return $base;
    }

    /**
     * @param $array
     * @return bool
     */
    public static function isAssociative($array)
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }
} 