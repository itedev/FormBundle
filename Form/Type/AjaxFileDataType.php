<?php

namespace ITE\FormBundle\Form\Type;

use ITE\FormBundle\Form\DataTransformer\FileToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AjaxFileDataType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxFileDataType extends AbstractType
{
    /**
     * @var string
     */
    private $uploadDir;

    /**
     * @param string $uploadDir
     */
    public function __construct($uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->addViewTransformer(new FileToStringTransformer(
                $options['multiple'],
                $this->uploadDir
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Symfony\Component\HttpFoundation\File\File',
            'empty_data' => null,
            'multiple' => false,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'hidden';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_ajax_file_data';
    }
}
