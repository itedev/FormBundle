<?php

namespace ITE\FormBundle\Form\Extension\AjaxToken;

use ITE\FormBundle\Util\UrlUtils;
use Symfony\Component\Form\FormInterface;
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
     * @param $form
     * @return string|void
     */
    public function getAjaxTokenFromForm(FormInterface $form)
    {
        $a = 1;
    }

    /**
     * @param $ajaxTokenFieldName
     * @return string
     */
    public function getAjaxToken($ajaxTokenFieldName)
    {
        if (null !== $ajaxToken = $this->request->get($ajaxTokenFieldName, null, true)) {
            return $ajaxToken;
        }

        $generator = new SecureRandom();

        return sha1($generator->nextBytes(20));
    }
} 