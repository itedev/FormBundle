<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\Component;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class HierarchicalComponent
 * @package ITE\FormBundle\SF\Component
 */
class HierarchicalComponent extends Component
{
    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        return array('@ITEFormBundle/Resources/public/js/component/hierarchical.js');
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'hierarchical';
    }
}