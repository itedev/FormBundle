<?php

namespace ITE\FormBundle\EventListener;

use ITE\FormBundle\Annotation\EntityConverter;
use ITE\FormBundle\EntityConverter\ConverterManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class KernelListener
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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
        if (!$event->isMasterRequest() || !$event->getRequest()->attributes->has('_entity_converter')) {
            return;
        }

        /** @var EntityConverter $annotation */
        $annotation = $event->getRequest()->attributes->get('_entity_converter');

        $converter = $this->converterManager->getConverter($annotation->getAlias());
        $result = $event->getControllerResult();
        if ($result instanceof Response) {
            return;
        }

        $options = $annotation->getOptions();
        $options['multiple'] = $annotation->isMultiple();
        $options['entity_options_callback'] = $annotation->getEntityOptionsCallback();
        $options['entity_options_callback_arguments'] = $annotation->getEntityOptionsCallbackArguments();
        $convertedResult = $converter->convert($result, $options);

        $event->setControllerResult($convertedResult);
    }
}
