<?php

namespace ITE\FormBundle\Service\Security;

/**
 * Interface ParamConverterInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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