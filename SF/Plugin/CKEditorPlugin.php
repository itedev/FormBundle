<?php

namespace ITE\FormBundle\SF\Plugin;

use ITE\FormBundle\SF\ChoicePlugin;
use ITE\FormBundle\SF\AbstractPlugin;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\FileLoader;

/**
 * Class CKEditorPlugin
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CKEditorPlugin extends AbstractPlugin
{
    /**
     * {@inheritdoc}
     */
    public static function getName()
    {
        return 'ckeditor';
    }
}
