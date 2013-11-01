<?php

namespace ITE\FormBundle\Util;

/**
 * Class UrlUtils
 * @package ITE\FormBundle\Util
 */
class UrlUtils
{
    /**
     * @param $url
     * @param $paramName
     * @param $paramValue
     * @return string
     */
    public static function addGetParameter($url, $paramName, $paramValue)
    {
        $queryString = @parse_url($url, PHP_URL_QUERY);

        $url .= $queryString ? '&' : '?';
        $url .= $paramName . '=' . $paramValue;

        return $url;
    }
} 