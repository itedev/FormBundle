<?php

namespace ITE\FormBundle\Util;

/**
 * Class MoneyUtils
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MoneyUtils
{
    /**
     * @param string $moneyPattern
     * @return array
     */
    public static function parseMoneyPattern($moneyPattern)
    {
        $result = [
            'symbol' => null,
            'position' => null,
        ];

        if (preg_match('~^(?P<prefix>.*?) ?{{ widget }} ?(?P<suffix>.*?)$~', $moneyPattern, $matches)) {
            if (!empty($matches['prefix'])) {
                $result['symbol'] = $matches['prefix'];
                $result['position'] = 'prefix';
            } elseif (!empty($matches['suffix'])) {
                $result['symbol'] = $matches['suffix'];
                $result['position'] = 'suffix';
            }
        }

        return $result;
    }
}
