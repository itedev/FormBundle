<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\FormBundle\SF\Plugin;

/**
 * Class BootstrapDatetimepickerPlugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class BootstrapDatetimepickerPlugin extends Plugin
{
    /**
     * Format date from PHP format to JS datetimepeeker format.
     *
     * @param $format
     * @return string
     */
    public static function formatPHPDateTimeFormat($format)
    {
        return strtr($format, [
            'dd'   => 'DD',    // day in month (02)
            'd'    => 'D',     // day in month (2)
            'yyyy' => 'YYYY',  // year (2015)
            'yy'   => 'YY',    // year (15)
            'y'    => 'YYYY',  // year (2015)
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'bootstrap_datetimepicker';
    }
}