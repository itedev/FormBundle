<?php
//
//namespace ITE\FormBundle\Twig\TokenParser;
//
//use Symfony\Bundle\AsseticBundle\Twig\AsseticNode;
//use Assetic\Asset\AssetInterface;
//use Assetic\Factory\AssetFactory;
//use Symfony\Bundle\AsseticBundle\Twig\AsseticTokenParser as BaseAsseticTokenParser;
//
///**
// * Class AsseticTokenParser
// * @package ITE\FormBundle\Twig\TokenParser
// */
//class AsseticTokenParser extends BaseAsseticTokenParser
//{
//    /**
//     * @param AssetInterface $asset
//     * @param \Twig_NodeInterface $body
//     * @param array $inputs
//     * @param array $filters
//     * @param $name
//     * @param array $attributes
//     * @param int $lineno
//     * @param null $tag
//     * @return AsseticNode
//     */
//    protected function createNode(AssetInterface $asset, \Twig_NodeInterface $body, array $inputs, array $filters, $name, array $attributes = array(), $lineno = 0, $tag = null)
//    {
//        if ('javascripts' === $tag) {
//            $inputs[] = '@ITEFormBundle/Resources/public/js/plugins/sf.select2.js';
//        }
//
//        return parent::createNode($asset, $body, $inputs, $filters, $name, $attributes, $lineno, $tag);
//    }
//}