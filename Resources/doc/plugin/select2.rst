Select2
~~~~~~~

Homepage: http://ivaynberg.github.io/select2/

Provided field types:

+------------------------------+---------------------+-----------------------+
| Type                         | Parent type         | Required components   |
+==============================+=====================+=======================+
| ite\_select2\_choice         | choice              | none                  |
+------------------------------+---------------------+-----------------------+
| ite\_select2\_language       | language            | none                  |
+------------------------------+---------------------+-----------------------+
| ite\_select2\_country        | country             | none                  |
+------------------------------+---------------------+-----------------------+
| ite\_select2\_timezone       | timezone            | none                  |
+------------------------------+---------------------+-----------------------+
| ite\_select2\_locale         | locale              | none                  |
+------------------------------+---------------------+-----------------------+
| ite\_select2\_currency       | currency            | none                  |
+------------------------------+---------------------+-----------------------+
| ite\_select2\_entity         | entity              | none                  |
+------------------------------+---------------------+-----------------------+
| ite\_select2\_document       | document            | none                  |
+------------------------------+---------------------+-----------------------+
| ite\_select2\_model          | model               | none                  |
+------------------------------+---------------------+-----------------------+
| ite\_select2\_ajax\_entity   | ite\_ajax\_entity   | dynamic\_choice       |
+------------------------------+---------------------+-----------------------+
| ite\_select2\_google\_font   | ite\_google\_font   | none                  |
+------------------------------+---------------------+-----------------------+

Example configuration:

.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        plugins:
            select2:    ~

Few words about ``ite_select2_ajax_entity``. This type does not load all entities at once and use AJAX autocomplete
instead of it.

Usage:

.. code-block:: php

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

.. code-block:: php

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

.. note ::
    it's strongly recommended to set obviously property as a second parameter in ``convertEntitiesToOptions`` method.