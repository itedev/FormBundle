<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\AbstractComponent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class HierarchicalComponent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class HierarchicalComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        return [
            '@ITEFormBundle/Resources/public/js/component/Hierarchical/jquery.hierarchical.js',
            '@ITEFormBundle/Resources/public/js/component/Hierarchical/hierarchical.js',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'hierarchical';
    }
}