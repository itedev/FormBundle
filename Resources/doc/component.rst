Components
----------

.. toctree::
    :hidden:

    component/collection
    component/dynamic_choice
    component/hierarchical
    component/ajax_file_upload
    component/editable
    component/ordered
    component/validation
    component/debug

- :doc:`Collection <component/collection>`
- :doc:`Dynamic Choice <component/dynamic_choice>`
- :doc:`Hierarchical <component/hierarchical>`
- :doc:`AJAX File Upload <component/ajax_file_upload>`
- :doc:`Editable <component/editable>`
- :doc:`Ordered <component/ordered>`
- :doc:`Validation <component/validation>`
- :doc:`Debug <component/debug>`

Add new component
-----------------

Create new class that extends ``ITE\FormBundle\SF\Component`` class or implements
``ITE\FormBundle\SF\ExtensionInterface``.

.. code-block :: php
namespace ITE\FormBundle\SF\Component;

use Acme\DemoBundle\SF\Component;

class FooComponent extends Component
{
    const NAME = 'foo'; // define id for your component

    public function loadConfiguration(FileLoader $loader, array $config, ContainerBuilder $container)
    {
        // ...
    }
}


Register new service with tag ``ite_form.component``.

.. configuration-block ::

    .. code-block :: yaml
        services:
            acme_demo.component.foo:
                class: Acme\DemoBundle\SF\Component\FooComponent
                tags:
                    - { name: ite_form.component }