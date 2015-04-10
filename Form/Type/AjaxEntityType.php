<?php

namespace ITE\FormBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AjaxEntityType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxEntityType extends AbstractType
{
    /**
     * @var ManagerRegistry $registry
     */
    protected $registry;

    /**
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $registry = $this->registry;

        $emNormalizer = function (Options $options, $em) use ($registry) {
            /* @var ManagerRegistry $registry */
            if (null !== $em) {
                if ($em instanceof ObjectManager) {
                    return $em;
                }

                return $registry->getManager($em);
            }

            $em = $registry->getManagerForClass($options['class']);

            if (null === $em) {
                throw new \RuntimeException(sprintf(
                    'Class "%s" seems not to be a managed Doctrine entity. '.
                    'Did you forget to map it?',
                    $options['class']
                ));
            }

            return $em;
        };

        $resolver->setDefaults(array(
            'em' => null,
            'property' => null,
        ));

        $resolver->setRequired(array('class'));

        $resolver->setNormalizers(array(
            'em' => $emNormalizer,
        ));

        $resolver->setAllowedTypes(array(
            'em' => array('null', 'string', 'Doctrine\Common\Persistence\ObjectManager'),
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_ajax_choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_ajax_entity';
    }
}