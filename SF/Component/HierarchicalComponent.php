<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\Component;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class HierarchicalComponent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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