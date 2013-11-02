<?php

namespace ITE\FormBundle\Form\Extension\AjaxToken;

/**
 * Interface AjaxTokenProviderInterface
 * @package ITE\FormBundle\Form\Extension\AjaxToken
 */
interface AjaxTokenProviderInterface
{
    /**
     * @param $ajaxTokenFieldName
     * @return string
     */
    public function generateAjaxToken($ajaxTokenFieldName);
} 