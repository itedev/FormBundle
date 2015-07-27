<?php

namespace ITE\FormBundle\Form\Type\Plugin\ICheck;

use ITE\FormBundle\Form\Type\Plugin\Core\AbstractPluginType;
use ITE\FormBundle\SF\Form\ClientFormTypeInterface;
use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\ICheckPlugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractChoiceType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AbstractChoiceType extends AbstractPluginType implements ClientFormTypeInterface
{
    /**
     * @var string $type
     */
    protected $type;

    /**
     * @param $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            0,
            'ite_icheck'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        $clientView->setOption('plugins', [
            ICheckPlugin::getName() => [
                'extras' => (object) [],
                'options' => (object) array_replace_recursive($this->options, $options['plugin_options'])
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
        $resolver->setDefaults([
            'expanded' => true,
        ]);
        $resolver->setAllowedValues([
            'expanded' => [true],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_icheck_' . $this->type;
    }
}