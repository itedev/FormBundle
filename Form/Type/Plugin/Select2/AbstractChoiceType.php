<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\Form\Type\Plugin\AbstractPluginType;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class AbstractChoiceType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AbstractChoiceType extends AbstractPluginType
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
        if ('entity' === $this->type) {
            $view->vars['attr']['data-property'] = $options['property'];
        }

        $view->vars['plugins'][Select2Plugin::getName()] = [
            'extras' => (object) [],
            'options' => (object) array_replace_recursive($this->options, $options['plugin_options'])
        ];

        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            0,
            'ite_select2'
        );
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
        return 'ite_select2_' . $this->type;
    }
}