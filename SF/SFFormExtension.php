<?php

namespace ITE\FormBundle\SF;

use ITE\JsBundle\EventListener\Event\AjaxRequestEvent;
use ITE\JsBundle\EventListener\Event\AjaxResponseEvent;
use ITE\JsBundle\SF\SFExtension;

/**
 * Class SFFormExtension
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class SFFormExtension extends SFExtension implements SFFormExtensionInterface
{
    /**
     * @var array
     */
    protected $components = [];

    /**
     * @var array
     */
    protected $plugins = [];

    /**
     * @var FormBag
     */
    public $formBag;

    public function __construct()
    {
        $this->formBag = new FormBag();
    }

    /**
     * {@inheritdoc}
     */
    public function getStylesheets()
    {
        $stylesheets = [];
        foreach ($this->getComponents() as $component) {
            /** @var $component ExtensionInterface */
            $stylesheets = array_merge($stylesheets, $component->getStylesheets());
        }
        foreach ($this->getPlugins() as $plugin) {
            /** @var $plugin ExtensionInterface */
            $stylesheets = array_merge($stylesheets, $plugin->getStylesheets());
        }

        return $stylesheets;
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        $javascripts = ['@ITEFormBundle/Resources/public/js/sf.form.js'];
        foreach ($this->getComponents() as $component) {
            /** @var $component ExtensionInterface */
            $javascripts = array_merge($javascripts, $component->getJavascripts());
        }
        foreach ($this->getPlugins() as $plugin) {
            /** @var $plugin ExtensionInterface */
            $javascripts = array_merge($javascripts, $plugin->getJavascripts());
        }

        return $javascripts;
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnStylesheets($debug)
    {
        $stylesheets = [];
        foreach ($this->getPlugins() as $plugin) {
            /** @var $plugin ExtensionInterface */
            if ($plugin->isCdnEnabled()) {
                $stylesheets = array_merge($stylesheets, $plugin->getCdnStylesheets($debug));
            }
        }

        return $stylesheets;
    }

    /**
     * {@inheritdoc}
     */
    public function getCdnJavascripts($debug)
    {
        $javascripts = [];
        foreach ($this->getPlugins() as $plugin) {
            /** @var $plugin ExtensionInterface */
            if ($plugin->isCdnEnabled()) {
                $javascripts = array_merge($javascripts, $plugin->getCdnJavascripts($debug));
            }
        }

        return $javascripts;
    }

    /**
     * {@inheritdoc}
     */
    public function dump()
    {
        $dump = '';
        if ($this->formBag->count()) {
            $dump .= 'SF.forms.set(' . json_encode($this->formBag->toArray()) . ');';
        }

        return $dump;
    }

    /**
     * {@inheritdoc}
     */
    public function onAjaxResponse(AjaxResponseEvent $event)
    {
        if ($this->formBag->count()) {
            $event->getAjaxDataBag()->addBodyData('forms', $this->formBag->toArray());
        }
    }


    /**
     * @param string $alias
     * @param ExtensionInterface $component
     */
    public function addComponent($alias, ExtensionInterface $component)
    {
        $this->components[$alias] = $component;
    }

    /**
     * Get components
     *
     * @return array
     */
    public function getComponents()
    {
        return $this->components;
    }

    /**
     * @param string $alias
     * @param ExtensionInterface $plugin
     */
    public function addPlugin($alias, ExtensionInterface $plugin)
    {
        $this->plugins[$alias] = $plugin;
    }

    /**
     * Get plugins
     *
     * @return array
     */
    public function getPlugins()
    {
        return $this->plugins;
    }

    /**
     * Get formBag
     *
     * @return FormBag
     */
    public function getFormBag()
    {
        return $this->formBag;
    }
}
