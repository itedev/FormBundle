<?php

namespace ITE\FormBundle\EventListener;

use ITE\FormBundle\Annotation\EntityConverter;
use ITE\FormBundle\Service\Converter\ConverterManagerInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class KernelListener
 * @package ITE\FormBundle\EventListener
 */
class KernelListener
{
    /**
     * @var ConverterManagerInterface $converterManager
     */
    private $converterManager;

    /**
     * @param ConverterManagerInterface $converterManager
     */
    public function __construct(ConverterManagerInterface $converterManager)
    {
        $this->converterManager = $converterManager;
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        if (!$event->isMasterRequest() || !$event->getRequest()->attributes->has('entity_converter')) {
            return;
        }

        /** @var EntityConverter $annotation */
        $annotation = $event->getRequest()->attributes->get('entity_converter');

        $converter = $this->converterManager->getConverter($annotation->getAlias());

        $result = $event->getControllerResult();

    }
}