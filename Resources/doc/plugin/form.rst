jQuery Form plugin
==================

Homepage
--------
http://malsup.com/jquery/form/

Configuration
-------------
.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        plugins:
            form:   ~

Usage
-----
.. code-block:: php

    // src/Acme/DemoBundle/Form/Type/FooType.php

    class FooType extends AbstractType
    {
        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver->setDefaults(array(
                'plugins' => 'form',
                // or
                // 'plugins' => array('form'),
                // or
                // 'plugins' => array(
                //     'form' => array(
                //         'option_name' => 'option_value',
                //     )
                // ),
            ));
        }
    }