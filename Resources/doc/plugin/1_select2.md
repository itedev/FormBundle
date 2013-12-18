### Select2

Homepage: http://ivaynberg.github.io/select2/

Provided field types:

| Type                    | Parent type     | Required components |
|-------------------------|-----------------|---------------------|
| ite_select2_choice      | choice          | none                |
| ite_select2_language    | language        | none                |
| ite_select2_country     | country         | none                |
| ite_select2_timezone    | timezone        | none                |
| ite_select2_locale      | locale          | none                |
| ite_select2_currency    | currency        | none                |
| ite_select2_entity      | entity          | none                |
| ite_select2_document    | document        | none                |
| ite_select2_model       | model           | none                |
| ite_select2_ajax_entity | ite_ajax_entity | dynamic_choice      |
| ite_select2_google_font | ite_google_font | none                |

Example configuration:

```yml
# app/config/config.yml

ite_form:
    plugins:
        select2:    ~
```

Few words about `ite_select2_ajax_entity`. This type does not load all entities at once and use AJAX autocomplete instead of it.

Usage:

```php
// src/Acme/DemoBundle/Form/Type/FooType.php

class FooType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bar', 'ite_select2_ajax_entity', array(
                'class' => 'AcmeDemoBundle:Bar',
                'property' => 'baz',
                'route' => 'acme_demo_foo_search_bar', // route for searching Bar records by given query
                'route_parameters' => array(), // optional
                // 'allow_create' => true,
                // 'create_route' => 'acme_demo_foo_create_bar', // route for creating Bar entity using given query
                'plugin_options' => array(
                    'placeholder' => 'Search bars',
                    'minimumInputLength' => 2,
                ),
            ))
        ;
    }
}
```

```php
// /src/Acme/DemoBundle/Controller/FooController.php

use FOS\RestBundle\Controller\Annotations\View;

class FooController extends Controller
{
    /**
     * @Route("/search", name="acme_demo_foo_search_bar")
     * @View()
     */
    public function searchBarAction(Request $request)
    {
        $term = $request->query->get('term');

        // get an array of entities
        $entities = $this->em->getRepository('AcmeDemoBundle:Bar')->yourSearchMethod($term);

        $property = $request->request->get('property');

        return $this->get('ite_form.select2.entity_converter')->convertEntitiesToOptions($entities, $property);
    }
}
```

**Note:** it's strongly recommended to set obviously property as a second parameter in `convertEntitiesToOptions` method.