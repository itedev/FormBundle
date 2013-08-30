<?php

namespace ITE\FormBundle\Twig\Extension;

use ITE\FormBundle\Twig\TokenParser\AsseticTokenParser;
use Assetic\Factory\AssetFactory;
use Assetic\ValueSupplierInterface;
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Bundle\AsseticBundle\Twig\AsseticExtension as BaseAsseticExtension;

/**
 * Class AsseticExtension
 * @package ITE\FormBundle\Twig\Extension
 */
class AsseticExtension extends BaseAsseticExtension
{
    private $templateNameParser;
    private $enabledBundles;

    /**
     * @param AssetFactory $factory
     * @param TemplateNameParserInterface $templateNameParser
     * @param bool $useController
     * @param array $functions
     * @param array $enabledBundles
     * @param ValueSupplierInterface $valueSupplier
     */
    public function __construct(AssetFactory $factory, TemplateNameParserInterface $templateNameParser,
                                $useController = false, $functions = array(), $enabledBundles = array(),
                                ValueSupplierInterface $valueSupplier = null)
    {
        parent::__construct($factory, $templateNameParser, $useController, $functions, $enabledBundles, $valueSupplier);

        $this->templateNameParser = $templateNameParser;
        $this->enabledBundles = $enabledBundles;
    }

    /**
     * @return array
     */
    public function getTokenParsers()
    {
        return array(
            $this->createTokenParser('javascripts', 'js/*.js'),
            $this->createTokenParser('stylesheets', 'css/*.css'),
            $this->createTokenParser('image', 'images/*', true),
        );
    }

    /**
     * @param $tag
     * @param $output
     * @param bool $single
     * @return AsseticTokenParser
     */
    private function createTokenParser($tag, $output, $single = false)
    {
        $tokenParser = new AsseticTokenParser($this->factory, $tag, $output, $single, array('package'));
        $tokenParser->setTemplateNameParser($this->templateNameParser);
        $tokenParser->setEnabledBundles($this->enabledBundles);

        return $tokenParser;
    }
}