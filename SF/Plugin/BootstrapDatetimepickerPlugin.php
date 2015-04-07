<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\FormBundle\SF\Plugin;

/**
 * Class BootstrapDatetimepickerPlugin
 * @package ITE\FormBundle\SF\Plugin
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
        return strtr($format,
            array(
//                'a'    => 'a',     // am/pm marker
//                'm'    => 'm',     // minute in hour
//                'mm'   => 'mm',    // minute in hour
//                'h'    => 'h',     // hour in am/pm (1~12)
//                'H'    => 'H',     // hour in day (0~23)
                'd'    => 'D',     // day in month (2)
                'dd'   => 'DD',    // day in month (02)
//                'MMMM' => 'MMMM',  // month in year (September)
//                'MMM'  => 'MMM',   // month in year (Sept)
//                'MM'   => 'MM',    // month in year (09)
//                'M'    => 'M',     // month in year (9)
                'yy'   => 'YY',    // year (15)
                'y'    => 'YYYY',  // year (2015)
                'yyyy' => 'YYYY',  // year (2015)
            )
        );
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'bootstrap_datetimepicker';
    }
}