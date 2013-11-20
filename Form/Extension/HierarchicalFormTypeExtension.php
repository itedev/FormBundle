<?php

namespace ITE\FormBundle\Form\Extension;

use ITE\FormBundle\Util\FormUtils;
use ITE\JsBundle\SF\SFExtensionInterface;
use RuntimeException;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class HierarchicalFormTypeExtension
 * @package ITE\FormBundle\Form\Extension
 */
class HierarchicalFormTypeExtension extends AbstractTypeExtension
{
    /**
     * @var SFExtensionInterface $sfForm
     */
    protected $sfForm;

    /**
     * @param SFExtensionInterface $sfForm
     * @param RouterInterface $router
     */
    public function __construct(SFExtensionInterface $sfForm, RouterInterface $router)
    {
        $this->sfForm = $sfForm;
        $this->router = $router;
    }

    /**
     * Get router
     *
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $type = $this;

        $dependsOnNormalizer = function (Options $options, $dependsOn) use ($type) {
            if (empty($dependsOn)) {
                return array();
            }

            foreach (array('fields', 'route') as $option) {
                if (!array_key_exists($option, $dependsOn)) {
                    throw new RuntimeException(sprintf('Missing "%s" sub-option inside "depends_on" option.', $option));
                }
                if (empty($dependsOn[$option])) {
                    throw new RuntimeException(sprintf('Empty "%s" sub-option inside "depends_on" option.', $option));
                }
            }
            $fields = $dependsOn['fields'];
            if (!is_array($fields) && !$fields instanceof \Traversable) {
                $fields = array($fields);
            }

            $routeParameters = isset($dependsOn['route_parameters'])
                ? (array) $dependsOn['route_parameters']
                : array();

            return array(
                'fields' => $fields,
                'url' => $type->getRouter()->generate($dependsOn['route'], $routeParameters),
            );
        };

        $resolver->setDefaults(array(
            'depends_on' => array(),
        ));
        $resolver->setNormalizers(array(
            'depends_on' => $dependsOnNormalizer,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (empty($options['depends_on'])) {
            return;
        }

        foreach ($options['depends_on']['fields'] as $name) {
            if (!isset($view->parent->children[$name])) {
                throw new RuntimeException(sprintf('Child "%s" does not exist.', $name));
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function finishView(FormView $view, FormInterface $form, array $options)
    {
        if (empty($options['depends_on'])) {
            return;
        }

        $dependsOn = $options['depends_on'];
        $selector = FormUtils::generateSelector($view);

        $parentView = $view->parent;
        $parents = array_map(function($field) use ($parentView) {
            return FormUtils::generateSelector($parentView->children[$field]);
        }, $dependsOn['fields']);

        $this->sfForm->getElementBag()->addHierarchicalElement($selector, $parents, array(
            'url' => $dependsOn['url'],
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getExtendedType()
    {
        return 'form';
    }
} 