<?php

namespace ITE\FormBundle\Form\Extension\AjaxToken;
use ITE\FormBundle\Util\UrlUtils;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Util\SecureRandom;

/**
 * Class DefaultAjaxTokenProvider
 * @package ITE\FormBundle\Form\Extension\AjaxToken
 */
class DefaultAjaxTokenProvider implements AjaxTokenProviderInterface
{
    /**
     * @var Request $request
     */
    protected $request;

    /**
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param $ajaxTokenFieldName
     * @return string
     */
    public function getAjaxToken($ajaxTokenFieldName)
    {
        if ($this->request->query->has($ajaxTokenFieldName)) {
            return $this->request->query->get($ajaxTokenFieldName);
        }

        return $this->generateAjaxToken();
    }

    /**
     * @param $action
     * @param $ajaxTokenFieldName
     * @param $ajaxTokenValue
     * @return string
     */
    public function addAjaxTokenToAction($action, $ajaxTokenFieldName, $ajaxTokenValue)
    {
        if (empty($action)) {
            $action = $this->request->getRequestUri();
        }
        if ($this->request->query->has($ajaxTokenFieldName)) {
            return $action;
        }

        return UrlUtils::addGetParameter($action, $ajaxTokenFieldName, $ajaxTokenValue);
    }

    /**
     * @return string
     */
    protected function generateAjaxToken()
    {
        $generator = new SecureRandom();

        return str_replace(array('+', '=', '/'), '', base64_encode($generator->nextBytes(20)));
    }
} 