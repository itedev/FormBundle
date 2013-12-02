<?php

namespace ITE\FormBundle\Service\Security;

use Symfony\Component\HttpFoundation\Session\Session;
use Zend\Crypt\BlockCipher;

/**
 * Class ParamProtector
 * @package ITE\FormBundle\Service\Security
 */
class ParamProtector implements ParamConverterInterface
{
    /**
     * @var Session $session
     */
    protected $session;

    /**
     * @var string $secret
     */
    protected $secret;

    /**
     * @param Session $session
     * @param $secret
     */
    public function __construct(Session $session, $secret)
    {
        $this->session = $session;
        $this->secret = $secret;
    }

    /**
     * @param $string
     * @return string
     */
    public function encrypt($string)
    {
        return $this->getBlockCipher()->encrypt($string);
    }

    /**
     * @param $string
     * @return bool|string
     */
    public function decrypt($string)
    {
        return $this->getBlockCipher()->decrypt($string);
    }

    /**
     * @return string
     */
    protected function generateKey()
    {
        return sha1($this->secret . $this->getSessionId());
    }

    /**
     * @return string
     */
    protected function getSessionId()
    {
        $this->session->start();

        return $this->session->getId();
    }

    /**
     * @return BlockCipher
     */
    protected function getBlockCipher()
    {
        $blockCipher = BlockCipher::factory('mcrypt', array('algo' => 'aes'));
        $blockCipher->setKey($this->generateKey());

        return $blockCipher;
    }
} 