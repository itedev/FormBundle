### Editable

This component provides ability to dynamically create and submit form for specific entity field(s).

Example configuration:

```yml
# app/config/config.yml

ite_form:
    components:
        editable: ~
```

Also this component include new `Editable` annotation. It can take 2 arguments: type and options (both are optional). These arguments can take the same values as corresponding arguments in `add` method of `FormBuilder`. You can omit annotation at all, and standard form guessers will be used instead.

Example usage:

```php
// src/Acme/DemoBundle/Entity/Foo.php

use ITE\FormBundle\Annotation as ITEForm;

class Foo
{
    /**
     * @ORM\Column(name="bar", type="string", length=255)
     * @ITEForm\Editable(type="textarea", options={
     *      "label": "Enter bar"
     * })
     */
    private $bar;
}
```