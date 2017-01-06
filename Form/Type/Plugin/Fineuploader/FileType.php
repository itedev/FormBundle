<?php

namespace ITE\FormBundle\Form\Type\Plugin\Fineuploader;

use ITE\FormBundle\SF\Plugin\FineuploaderPlugin;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class FileType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FileType extends AbstractType
{
    /**
     * @var array $options
     */
    protected $options;

    /**
     * @param $options
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'route' => 'ite_form_plugin_fineuploader_file_upload',
            'input_name' => 'qqfile',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (!isset($view->vars['plugins'])) {
            $view->vars['plugins'] = [];
        }
        $view->vars['plugins'][FineuploaderPlugin::getName()] = [
            'extras' => (object) [],
            'options' => array_replace_recursive(
                $this->options,
                $options['plugin_options'],
                [
                    'multiple' => $options['multiple'] ? 1 : 0,
                    'request' => [
                        'endpoint' => $view->vars['url'],
                        'inputName' => $options['input_name'],
                    ]
                ]
            )
        ];
        $view->vars['type'] = 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_ajax_file';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_fineuploader_file';
    }
}
