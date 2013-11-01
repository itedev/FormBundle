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
    public function getAjaxToken($ajaxTokenFieldName);

    /**
     * @param $action
     * @param $ajaxTokenFieldName
     * @param $ajaxTokenValue
     * @return string
     */
    public function addAjaxTokenToAction($action, $ajaxTokenFieldName, $ajaxTokenValue);
} 