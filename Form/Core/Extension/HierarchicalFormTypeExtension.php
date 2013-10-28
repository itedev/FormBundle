<?php

namespace ITE\FormBundle\Form\Core\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class HierarchicalFormTypeExtension
 * @package ITE\FormBundle\Form\Core\Extension
 */
class HierarchicalFormTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'depends_on' => array(),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
//        $dependsOn = $options['depends_on'];
//        if (!empty($dependsOn)) {
//            $siblings = $form->getParent()->all();
//            $propertyPath = $form->getPropertyPath();
//            $root = $form->getRoot();
//            // \Symfony\Component\Form\Extension\Validator\ViolationMapper\ViolationMapper
//            $a = 1;
//        }
//
//        $view->vars['depends_on'] = $options['depends_on'];
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
} 