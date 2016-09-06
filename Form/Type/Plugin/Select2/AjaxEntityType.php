<?php

namespace ITE\FormBundle\Form\Type\Plugin\Select2;

use ITE\FormBundle\SF\Form\ClientFormView;
use ITE\FormBundle\SF\Plugin\Select2Plugin;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AjaxEntityType
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxEntityType extends AbstractAjaxChoiceType
{
    /**
     * @var RouterInterface $router
     */
    private $router;

    /**
     * @param array $options
     * @param RouterInterface $router
     */
    public function __construct(array $options, RouterInterface $router)
    {
        parent::__construct($options);
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
        parent::setDefaultOptions($resolver);

        $self = $this;

        $createUrlNormalizer = function (Options $options, $createUrl) use ($self) {
            if (!$options['allow_create']) {
                return null;
            }

            if (!empty($options['create_route'])) {
                return $self->getRouter()->generate($options['create_route'], $options['create_route_parameters']);
            } elseif (!empty($createUrl)) {
                return $createUrl;
            } else {
                return null;
                //throw new \RuntimeException('You must specify "create_route" or "create_url" option.');
            }
        };

        $resolver->setDefaults([
            'allow_create' => false,
            'case_sensitive' => true,
            'create_route' => null,
            'create_route_parameters' => [],
            'create_url' => null,
            'create_option_format' => null, // available placeholders: %term%
        ]);
        $resolver->setNormalizers([
            'create_url' => $createUrlNormalizer,
        ]);
        $resolver->setAllowedTypes([
            'allow_create' => ['bool'],
            'case_sensitive' => ['bool'],
            'create_route' => ['null', 'string'],
            'create_route_parameters' => ['array'],
            'create_url' => ['null', 'string'],
            'create_option_format' => ['null', 'string'],
        ]);
        $resolver->setOptional([
            'create_route',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['attr']['data-property'] = $options['property'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildClientView(ClientFormView $clientView, FormView $view, FormInterface $form, array $options)
    {
        parent::buildClientView($clientView, $view, $form, $options);

        if (!$options['allow_create']) {
            return;
        }

        $plugins = $clientView->getOption('plugins', []);
        $pluginsExtras = $plugins[Select2Plugin::getName()]['extras'];

        $pluginsExtras['allow_create'] = true;
        $pluginsExtras['create_url'] = $options['create_url'];
        $pluginsExtras['create_option_format'] = $options['create_option_format'];
        $pluginsExtras['case_sensitive'] = $options['case_sensitive'];

        $plugins[Select2Plugin::getName()]['extras'] = $pluginsExtras;
        $clientView->setOption('plugins', $plugins);
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return 'ite_ajax_entity';
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'ite_select2_ajax_entity';
    }
}
