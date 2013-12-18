### Parsley

Homepage: http://parsleyjs.org/

Example configuration:

```yml
# app/config/config.yml

ite_form:
    components:
        validation:         ~
    plugins:
        parsley:
            enabled:        true
            options:
                trigger:    change
```

Usage:

```php
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
```