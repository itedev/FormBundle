<?php

namespace ITE\FormBundle\SF\Component;

use ITE\FormBundle\SF\Component;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class CollectionComponent
 * @package ITE\FormBundle\SF\Component
 */
class CollectionComponent extends Component
{
    const NAME = 'collection';

    /**
     * {@inheritdoc}
     */
    public function addFormResources(ContainerInterface $container)
    {
        return array(
            'ITEFormBundle:Form/Component/collection:fields.html.twig'
        );
    }

}