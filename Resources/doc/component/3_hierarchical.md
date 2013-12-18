### Hierarchical

This component allows you easily build hierarchical fields (it means that available options or value of some field(s) depends on selected value of another field(s)) with a couple of lines in FormType. It provides next features:
 * works not only for select, but for checkboxes, radios, inputs, textareas and supported plugins.
 * each element may have several parents (upon which it depends) and several children (on which it affects)
 * size of hierarchy is not limited
 * it works for fields inside newly created collection items (of course if you enable **collection** component)

How it works:

It bind listener on `change` event for each object in hierarchy, that have children. When element value is changed - it gather data from all element parents (not only direct), clear element and its children (not only direct) current values and load new options/value via AJAX call or JavaScript callback.

Example configuration:

```yml
# app/config/config.yml

ite_form:
    components:
        hierarchical: ~
```

Usage:

```php
// src/Acme/DemoBundle/Form/Type/FooType.php

class FooType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bar', 'entity', array(
                // ...
            ))
            ->add('baz', 'entity', array(
                // ...
                'hierarchical' => array(
                    'parents' => 'bar', // name of parent field inside same form
                    'route' => 'acme_demo_foo_baz', // you can set a route for retrieving new values
                    'route_parameters' => array(), // optional
                ),
            ))
            ->add('qux', 'entity', array(
                // ...
                'hierarchical' => array(
                    'parents' => array('baz'), // array of parent field names inside same form
                    'callback' => 'myApp.getQuxValues', // or you can set JavaScript callback for it
                ),
            ))
        ;
    }
}
```

```php
// src/Acme/DemoBundle/Controller/FooController.php

namespace Acme\DemoBundle\Controller;

use FOS\RestBundle\Controller\Annotations\View;
use JMS\DiExtraBundle\Annotation as DI;

class FooController extends Controller
{
    /**
     * @var EntityConverter
     *
     * @DI\Inject("ite_form.entity_converter")
     */
    protected $entityConverter;

    /**
     * Method for rendering select options
     *
     * @Route("/baz", name="acme_demo_foo_baz")
     * @View("ITEFormBundle:Form/Component/dynamic_choice:choices.html.twig")
     */
    public function bazAction(Request $request)
    {
        $data = $request->request->get('data');
        $barId = $data['bar'];

        $bazes = $this->em->getRepository('AcmeDemoBundle:Baz')->findByBar($barId);
        $property = 'qux';

        return array(
            'options' => $this->entityConverter->convertEntitiesToOptions($bazes, $property)
        );
    }

    /**
     * Method for rendering checkboxes or radios
     *
     * @Route("/expanded-baz", name="acme_demo_foo_expanded_baz")
     * @View("ITEFormBundle:Form/Component/dynamic_choice:expanded_choices.html.twig")
     */
    public function expandedBazAction(Request $request)
    {
        $data = $request->request->get('data');
        $propertyPath = $request->request->get('propertyPath'); // property_path (or full_name) of the field is needed for rendering expanded choices

        $barId = $data['bar'];

        $bazes = $this->em->getRepository('AcmeDemoBundle:Baz')->findByBar($barId);
        $property = 'qux';
        $choices = $this->entityConverter->convertEntitiesToChoices($bazes, $property);

        return array(
            'form' => $this->get('ite_form.widget_generator')->createChoiceView($propertyPath, $choices);
        );
    }
}
```

```js
// src/Acme/DemoBundle/Resources/public/js/foo.js

function getQuxValues(element, data) {
    var bar = data['bar']; // yes, you have a values of all parents, not only direct
    var baz = data['baz'];

    var options = ...;

    return options;
}
```

Events:
 * ite-before-clear.hierarchical - triggers before element value will be cleared. If return `false` default function will not be executed.
 * ite-clear.hierarchical - triggers after element value was cleared.