<?php

namespace ITE\FormBundle\Util;

/**
 * Class MomentJsUtils
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MomentJsUtils
{
    /**
     * @param string $format
     * @return string
     */
    public static function icuToMomentJs($format)
    {
        return strtr($format, [
            'D'      => 'DDD',   // day of year (189)
            'dd'     => 'DD',    // day in month (02)
            'd'      => 'D',     // day in month (2)
            'EEEEEE' => 'dd',    // day of week (Tu)
            'EEEE'   => 'dddd',  // day of week (Tuesday)
            'EEE'    => 'ddd',   // day of week (Tue)
            'EE'     => 'ddd',   // day of week (Tue)
            'E'      => 'ddd',   // day of week (Tue)
            'eeeeee' => 'dd',    // day of week (Tu)
            'eeee'   => 'dddd',  // day of week (Tuesday)
            'eee'    => 'ddd',   // day of week (Tue)
            'e'      => 'E',     // local day of week (2)
            'xxx'    => 'Z',     // Time Zone: ISO8601 extended hm, without Z (-08:00)
            'xx'     => 'ZZ',    // Time Zone: ISO8601 basic hm, without Z (-0800)
            'yyyy'   => 'YYYY',  // year (2015)
            'yy'     => 'YY',    // year (15)
            'y'      => 'YYYY',  // year (2015)
            'ZZZZZ'  => 'Z',     // TIme Zone: ISO8601 extended hms? (=XXXXX) (-08:00, -07:52:58, Z)
            'ZZZ'    => 'ZZ',    // Time Zone: ISO8601 basic hms? / RFC 822 (-0800)
            'Z'      => 'ZZ',    // Time Zone: ISO8601 basic hms? / RFC 822 (-0800)
            'zzz'    => 'z',     // Time Zone: specific non-location (PDT)
            'zz'     => 'z',     // Time Zone: specific non-location (PDT)
            '\'T\''  => 'T',
        ]);
    }
}