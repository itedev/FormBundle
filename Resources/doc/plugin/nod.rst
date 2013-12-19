Nod
~~~

Homepage: http://casperin.github.io/nod/

Example configuration:

.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        components:
            validation: ~
        plugins:
            nod:        ~

Usage:

.. code-block:: php

    // src/Acme/DemoBundle/Form/Type/FooType.php

    class FooType extends AbstractType
    {
        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver->setDefaults(array(
                'plugins' => 'nod',
                // or
                // 'plugins' => array('nod'),
                // or
                // 'plugins' => array(
                //     'nod' => array(
                //         'option_name' => 'option_value',
                //     )
                // ),
            ));
        }
    }