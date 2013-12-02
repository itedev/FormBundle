<?php

namespace ITE\FormBundle\Service\Security;

/**
 * Interface ParamConverterInterface
 * @package ITE\FormBundle\Service\Security
 */
interface ParamConverterInterface
{
    /**
     * @param $string
     * @return string
     */
    public function encrypt($string);

    /**
     * @param $string
     * @return bool|string
     */
    public function decrypt($string);
} 