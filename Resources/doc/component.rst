Components
==========

Available components
--------------------

.. toctree::
    :hidden:

    component/collection/index
    component/collection/demo
    component/dynamic_choice/index
    component/dynamic_choice/demo
    component/hierarchical/index
    component/hierarchical/demo
    component/ajax_file_upload
    component/editable
    component/ordered/index
    component/ordered/demo
    component/validation
    component/debug

- :doc:`Collection <component/collection/index>`
- :doc:`Dynamic Choice <component/dynamic_choice/index>`
- :doc:`Hierarchical <component/hierarchical/index>`
- :doc:`AJAX File Upload <component/ajax_file_upload>`
- :doc:`Editable <component/editable>`
- :doc:`Ordered <component/ordered/index>`
- :doc:`Validation <component/validation>`
- :doc:`Debug <component/debug>`

Add new component
-----------------
Create new class that extends ``ITE\FormBundle\SF\Component`` class or implements
``ITE\FormBundle\SF\ExtensionInterface``.

.. code-block:: php

    namespace Acme\DemoBundle\SF\Component;

    use ITE\FormBundle\SF\Component;

    class FooComponent extends Component
    {
        const NAME = 'foo'; // define id for your component

        public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
        {
            // ...
        }
    }

And register new service for it with tag ``ite_form.component``.

.. configuration-block::

    .. code-block:: yaml

        services:
            acme_demo.component.foo:
                class: Acme\DemoBundle\SF\Component\FooComponent
                tags:
                    - { name: ite_form.component }