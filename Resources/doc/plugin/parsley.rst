Parsley
~~~~~~~

Homepage: http://parsleyjs.org/

Example configuration:

.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        components:
            validation:         ~
        plugins:
            parsley:
                enabled:        true
                options:
                    trigger:    change

Usage:

.. code-block:: php

    // src/Acme/DemoBundle/Form/Type/FooType.php

    class FooType extends AbstractType
    {
        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver->setDefaults(array(
                'plugins' => 'parsley',
                // or
                // 'plugins' => array('parsley'),
                // or
                // 'plugins' => array(
                //     'parsley' => array(
                //         'option_name' => 'option_value',
                //     )
                // ),
            ));
        }
    }