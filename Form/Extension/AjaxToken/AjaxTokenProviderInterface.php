<?php

namespace ITE\FormBundle\Form\Extension\AjaxToken;

/**
 * Interface AjaxTokenProviderInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface AjaxTokenProviderInterface
{
    /**
     * @param $ajaxTokenFieldName
     * @return string
     */
    public function getAjaxToken($ajaxTokenFieldName);
} 