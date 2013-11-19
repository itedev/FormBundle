<?php

namespace ITE\FormBundle\Form\EventListener;

use ITE\FormBundle\Service\File\FileManagerInterface;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileuploadSubscriber
 * @package ITE\FormBundle\Form\EventListener
 */
class FileuploadSubscriber implements EventSubscriberInterface
{
    /**
     * @var FileManagerInterface
     */
    protected $fileManager;

    /**
     * @param FileManagerInterface $fileManager
     */
    public function __construct(FileManagerInterface $fileManager)
    {
        $this->fileManager = $fileManager;
    }

    /**
     * @inheritdoc
     */
    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SUBMIT => 'preSubmit',
        );
    }

    /**
     * @param FormEvent $event
     */
    public function preSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        $root = $form->getRoot();

        $ajaxToken = $root->getConfig()->getAttribute('ajax_token_value');
        $propertyPath = FormUtils::getFullName($form);

        $files = $this->fileManager->getFiles($ajaxToken, $propertyPath);
        if (!empty($files)) {
            /** @var $file File */
            $file = array_shift($files);

            $data = new UploadedFile(
                $file->getRealPath(),
                $file->getBasename(),
                $file->getMimeType(),
                $file->getSize(),
                null,
                true
            );

            $event->setData($data);
        }
    }

} 