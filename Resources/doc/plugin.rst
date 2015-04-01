Plugins
=======

Available plugins
-----------------

.. toctree::
    :hidden:

    plugin/select2/index
    plugin/select2/demo
    plugin/tinymce
    plugin/bootstrap_datetimepicker
    plugin/bootstrap_datetimepicker2
    plugin/bootstrap_colorpicker
    plugin/bootstrap_spinedit
    plugin/fileupload
    plugin/fineuploader
    plugin/minicolors
    plugin/knob
    plugin/starrating
    plugin/x_editable
    plugin/nod/index
    plugin/nod/demo
    plugin/parsley/index
    plugin/parsley/demo
    plugin/form

- :doc:`Select2 <plugin/select2>`
- :doc:`Tinymce <plugin/tinymce>`
- :doc:`Bootstrap Datetimepicker <plugin/bootstrap_datetimepicker>`
- :doc:`Bootstrap Datetimepicker <plugin/bootstrap_datetimepicker2>`
- :doc:`Bootstrap Colorpicker <plugin/bootstrap_colorpicker>`
- :doc:`Bootstrap Spinedit <plugin/bootstrap_spinedit>`
- :doc:`Fileupload <plugin/fileupload>`
- :doc:`Fineuploader <plugin/fineuploader>`
- :doc:`MiniColors <plugin/minicolors>`
- :doc:`Knob <plugin/knob>`
- :doc:`StarRating <plugin/starrating>`
- :doc:`X-Editable <plugin/x_editable>`
- :doc:`Nod! <plugin/nod>`
- :doc:`Parsley <plugin/parsley>`
- :doc:`Form <plugin/form>`

Add new plugin
--------------
Create new class that extends ``ITE\FormBundle\SF\Plugin`` class or implements
``ITE\FormBundle\SF\ExtensionInterface``.

.. code-block:: php

    namespace Acme\DemoBundle\SF\Form\Plugin;

    use ITE\FormBundle\SF\Plugin;

    class FooPlugin extends Plugin
    {
        public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
        {
            // ...
        }

        public static function getName()
        {
            return 'foo';
        }
    }

And register new service for it with tag ``ite_form.plugin``.

.. configuration-block::

    .. code-block:: yaml

        services:
            acme_demo.plugin.foo:
                class: Acme\DemoBundle\SF\Form\Plugin\FooPlugin
                tags:
                    - { name: ite_form.plugin }