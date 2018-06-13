<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\AbstractComponent;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class OldValueAwareComponent
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class OldValueAwareComponent extends AbstractComponent
{
    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        return [
            '@ITEFormBundle/Resources/public/js/component/OldValueAware/old_value_aware.js',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'old_value_aware';
    }
}
