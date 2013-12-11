<?php

namespace ITE\FormBundle\Util;

/**
 * Class LocaleUtils
 * @package ITE\FormBundle\Util
 */
class LocaleUtils
{
    /**
     * @param null $precision
     * @return int
     */
    public static function getPrecision($precision = null)
    {
        if ($precision) {
            return $precision;
        }

        $formatter = new \NumberFormatter(\Locale::getDefault(), \NumberFormatter::DECIMAL);

        return $formatter->getAttribute(\NumberFormatter::MAX_FRACTION_DIGITS);
    }
} 