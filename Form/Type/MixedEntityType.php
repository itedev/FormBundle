<?php

namespace ITE\FormBundle\Form\Type;

use Doctrine\Common\Persistence\ManagerRegistry;
use ITE\FormBundle\Form\ChoiceList\MixedEntityChoiceList;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class MixedEntityType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class MixedEntityType extends AbstractType
{
    /**
     * @var FormFactoryInterface $formFactory
     */
    private $formFactory;

    /**
     * @var array
     */
    private $mixedEntityChoiceListCache = [];

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        array_splice(
            $view->vars['block_prefixes'],
            array_search($this->getName(), $view->vars['block_prefixes']),
            1,
            [
                'entity'
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $mixedEntityChoiceListCache = &$this->mixedEntityChoiceListCache;
        $formFactory = $this->formFactory;

        $choiceList = function (Options $options) use (&$mixedEntityChoiceListCache, $formFactory) {
            $entitiesOptions = $options['options'];
            $group = $options['group'];

            $entityChoicesLists = [];
            $entityLabels = [];
            $entityChoiceListHashes = [];
            foreach ($entitiesOptions as $alias => $entityOptions) {
                $label = $entityOptions['label'];

                $entityOptions['group_by'] = null;
                unset($entityOptions['label']);

                $builder = $formFactory->createBuilder('entity', null, $entityOptions);
                $entityChoiceList = $builder->getOption('choice_list');

                $entityChoiceListHash = spl_object_hash($entityChoiceList);
                $entityChoiceListHashes[] = $entityChoiceListHash;

                $entityChoicesLists[$alias] = $entityChoiceList;
                $entityLabels[$alias] = $label;
            }

            $mixedEntityChoiceListHash = implode(',', $entityChoiceListHashes);
            if (!isset($mixedEntityChoiceListCache[$mixedEntityChoiceListHash])) {
                $mixedEntityChoiceListCache[$mixedEntityChoiceListHash] = new MixedEntityChoiceList(
                    $entityChoicesLists,
                    $entityLabels,
                    $group
                );
            }

            return $mixedEntityChoiceListCache[$mixedEntityChoiceListHash];
        };

        $resolver->setDefaults([
            'choice_list' => $choiceList,
            'group' => true,
        ]);
        $resolver->setRequired([
            'options',
        ]);
        $resolver->setAllowedTypes([
            'options' => 'array',
            'group' => 'bool',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'choice';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_mixed_entity';
    }
}
