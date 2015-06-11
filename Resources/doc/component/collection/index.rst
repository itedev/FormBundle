Collection component
====================

Overview
--------
- all field plugins **automatically** applied when new collection item is added **(and it works even for
  collections inside collection, collection inside collection inside collection, and so on)**
- new options and events are added to form collections, that make work with it much easier
- you can set global or per collection options
- theming of collections become more flexible, you can render it as a table, list or anything you want

Configuration
-------------
Simple configuration:

.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        components:
            collection: ~

Full configuration:

.. code-block:: yaml

    # app/config/config.yml
    ite_form:
        components:
            collection:
                enabled:              false

                # animation for showing new collection items
                widget_show_animation:
                    type:                 ~ # One of "show"; "slide"; "fade"

                    # time in ms
                    length:               0

                # animation for hiding collection items
                widget_hide_animation:
                    type:                 ~ # One of "hide"; "slide"; "fade"

                    # time in ms
                    length:               0

.. note ::
    For using it, don't forget to remove ``@MopaBootstrapBundle/Resources/public/js/mopabootstrap-collection.js``
    line from your ``javascripts`` tag.

Several new options are added to the collection field:

+---------------------------+---------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| Option                    | Default                                                 | Description                                                                                                                                                                                                                                                                                                                                    |
+===========================+=========================================================+================================================================================================================================================================================================================================================================================================================================================+
| collection\_item\_tag     | div                                                     | Root tag for collection item.                                                                                                                                                                                                                                                                                                                  |
+---------------------------+---------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| collection\_id            | By default it equals to field ``unique_block_prefix``   | Value of attribute ``data-collection-id`` that will be added to all collection instances of specific type. If you are using collection inside collection, it will be the same for all children collections. Also you can always get collection element for any inner element, by calling: ``$('selector').closest('[data-collection-id]')``.   |
+---------------------------+---------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| widget\_show\_animation   | array('type' => 'show', 'length' => 0)                  | Animation for showing new collection items. Available values for type: 'slide', 'fade', 'show'. Length is integer in ms.                                                                                                                                                                                                                       |
+---------------------------+---------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+
| widget\_hide\_animation   | array('type' => 'hide', 'length' => 0)                  | Animation for hiding collection items. Available values for type: 'slide', 'fade', 'hide'. Length is integer in ms.                                                                                                                                                                                                                            |
+---------------------------+---------------------------------------------------------+------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------+

Plugin triggers several useful events:

+--------------------------------+---------------+---------------------------------------------------------------------------------------------------------+
| Event                          | Arguments     | Description                                                                                             |
+================================+===============+=========================================================================================================+
| ite-before-add.collection      | event, item   | Event triggered before new collection item will be added. If return ``false`` item will not be added.   |
+--------------------------------+---------------+---------------------------------------------------------------------------------------------------------+
| ite-add.collection             | event, item   | Event triggered after new collection item was added.                                                    |
+--------------------------------+---------------+---------------------------------------------------------------------------------------------------------+
| ite-before-remove.collection   | event, item   | Event triggered before collection item will be removed. If return ``false`` item will not be removed.   |
+--------------------------------+---------------+---------------------------------------------------------------------------------------------------------+
| ite-remove.collection          | event, item   | Event triggered after collection item was removed.                                                      |
+--------------------------------+---------------+---------------------------------------------------------------------------------------------------------+

Example of usage:

.. code-block:: js

    // for root collections
    $('collection_selector').on('ite-add.collection ite-remove.collection', function(e, item) {
      var collection = $(this);

      $('#collection_count').html(collection.collection('count'));
    });

    // for children collections
    $('root_collection_selector').on('ite-add.collection ite-remove.collection', 'child_collection_selector', function(e, item) {
      var collection = $(this);

      $('#child_collection_count').html(collection.collection('count'));

      e.stopPropagation(); // stop event propagation to parent collections
    });

.. note ::
    for root collection selector you can use anything you like: ``#id``, ``.class`` or ``[data-collection-id="..."]``.
    For children collections it is easier to use ``[data-collection-id="..."]`` selector (or class if you added it).

Collection plugin methods:

+----------------+-----------------------------------------+-----------------------------------------------------------------------------------------------------------------------------+
| Method         | Arguments                               | Description                                                                                                                 |
+================+=========================================+=============================================================================================================================+
| add            | None                                    | Add new collection item. It is automatically called when you click on element with ``data-collection-add-btn`` attribute.   |
+----------------+-----------------------------------------+-----------------------------------------------------------------------------------------------------------------------------+
| remove         | Any $(element) inside collection item   | Remove corresponding collection item.                                                                                       |
+----------------+-----------------------------------------+-----------------------------------------------------------------------------------------------------------------------------+
| items          | None                                    | Return jQuery collection of all collection items.                                                                           |
+----------------+-----------------------------------------+-----------------------------------------------------------------------------------------------------------------------------+
| count          | None                                    | Return number of collection items.                                                                                          |
+----------------+-----------------------------------------+-----------------------------------------------------------------------------------------------------------------------------+
| parents        | None                                    | Return all parent collection (if exist) (parent elements with ``data-collection-id`` attribute).                            |
+----------------+-----------------------------------------+-----------------------------------------------------------------------------------------------------------------------------+
| parentsCount   | None                                    | Return number of parent collections.                                                                                        |
+----------------+-----------------------------------------+-----------------------------------------------------------------------------------------------------------------------------+
| hasParent      | None                                    | Return ``true`` if collection has parent collection, false otherwise.                                                       |
+----------------+-----------------------------------------+-----------------------------------------------------------------------------------------------------------------------------+
| itemsWrapper   | None                                    | Return collection items wrapper jQuery object (with class ``collection-items``).                                            |
+----------------+-----------------------------------------+-----------------------------------------------------------------------------------------------------------------------------+

Example of using usual collection:

.. code-block:: php

    // src/Acme/DemoBundle/Form/Type/FooType.php

    class FooType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('bars', 'collection', array(
                    'type' => new BarType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'by_reference' => false,
                    'widget_add_btn' => array(
                        'label' => 'Add bar',
                        'icon' => 'plus-sign'
                    ),
                    'options' => array(
                        'label' => false,
                        'widget_control_group' => false,
                    ),
                ))
            ;
        }
    }

.. code-block:: html+jinja

    {# src/Acme/DemoBundle/Resources/views/foo.html.twig #}

    {% block _foo_bars_entry_widget %}
        <div class="row-fluid">
            <div class="span4">
                {{ form_row(form.baz) }}
            </div>
            <div class="span4">
                {{ form_row(form.qux) }}
            </div>
            <div class="span4">
                <a href="#" class="btn" data-collection-remove-btn>
                    <i class="icon-remove-sign"></i>
                    Remove
                </a>
            </div>
        </div>
    {% endblock _foo_bars_entry_widget %}

Example of using collection with table template:

.. code-block:: php

    // src/Acme/DemoBundle/Form/Type/FooType.php

    class FooType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('bars', 'collection', array(
                    'type' => new BarType(),
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'by_reference' => false,
                    'collection_item_tag' => 'tr', // Note: 'tr', not 'div'
                    'widget_add_btn' => array(
                        'label' => 'Add bar',
                        'icon' => 'plus-sign'
                    ),
                    'options' => array(
                        'label' => false,
                        'widget_control_group' => false,
                    ),
                ))
            ;
        }
    }

.. code-block:: html+jinja

    {# src/Acme/DemoBundle/Resources/views/foo.html.twig #}

    {% block _foo_bars_widget %}
        <table class="table table-striped table-bordered table-hover table-condensed">
            <thead>
                <tr>
                    <th>Baz</th>
                    <th>Qux</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="collection-items"> {# Note: we must add 'collection-items' class to items container #}
                {{ block('form_widget') }} {# Note: form_widget, not collection_widget #}
            </tbody>
        </table>
        {{ block('form_widget_add_btn') }} {# render add btn by manual calling needed block #}
    {% endblock _foo_bars_widget %}

    {% block _foo_bars_entry_widget %}
        <td>
            {{ form_row(form.baz) }}
        </td>
        <td>
            {{ form_row(form.qux) }}
        </td>
        <td>
            <a href="#" class="btn" data-collection-remove-btn>
                <i class="icon-remove-sign"></i>
                Remove
            </a>
        </td>
    {% endblock _foo_bars_entry_widget %}

You can render add and remove button manually, if you will follow next instructions:

- add button: add ``data-collection-add-btn`` attribute and place it inside collection element (element with
  'data-collection-id' attribute).
- remove button: add ``data-collection-remove-btn`` attribute and place it inside collection item element
  (element with '.collection-item' class).