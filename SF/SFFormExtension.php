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
        $inputs = [];

        // add component css
        foreach ($this->getComponents() as $component) {
            /** @var $component ExtensionInterface */
            $inputs = array_merge($inputs, $component->getStylesheets());
        }

        // add plugin css
        foreach ($this->getPlugins() as $plugin) {
            /** @var $plugin ExtensionInterface */
            $inputs = array_merge($inputs, $plugin->getStylesheets());
        }

        return $inputs;
    }

    /**
     * {@inheritdoc}
     */
    public function getJavascripts()
    {
        $inputs = ['@ITEFormBundle/Resources/public/js/sf.form.js'];

        // add component js
        foreach ($this->getComponents() as $component) {
            /** @var $component ExtensionInterface */
            $inputs = array_merge($inputs, $component->getJavascripts());
        }

        // add plugin js
        foreach ($this->getPlugins() as $plugin) {
            /** @var $plugin ExtensionInterface */
            $inputs = array_merge($inputs, $plugin->getJavascripts());
        }

        return $inputs;
    }

    /**
     * {@inheritdoc}
     */
    public function getInlineJavascripts()
    {
        $dump = '';
        $dump .= '(function($){$(function(){';

        if ($this->formBag->count()) {
            $dump .= 'SF.forms.set(' . json_encode($this->formBag->toArray()) . ');';
            $dump .= 'SF.forms.initialize();';
        }

        $dump .= '});})(jQuery);';

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