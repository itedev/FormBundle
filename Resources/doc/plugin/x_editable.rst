X-Editable plugin
=================

Homepage
--------
http://vitalets.github.io/x-editable/

Configuration
-------------
.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        components:
            editable:   ~
        plugins:
            x_editable: ~

Provided twig filters
---------------------

.. code-block:: html+jinja

    {# src/Acme/DemoBundle/Resources/views/foo.html.twig #}

    {{ foo|ite_x_editable('bar') }} {# available arguments: field, text, options, attr #}

+------------+---------------+-----------+----------------------------------------------------------------------------------------------------------------------+
| Argument   | Type          | Default   | Description                                                                                                          |
+============+===============+===========+======================================================================================================================+
| field      | string        |           | Field or association name inside entity                                                                              |
+------------+---------------+-----------+----------------------------------------------------------------------------------------------------------------------+
| text       | string/null   | null      | Initial text for link, if not set - taken from corresponding entity field value                                      |
+------------+---------------+-----------+----------------------------------------------------------------------------------------------------------------------+
| options    | object        | {}        | Options for X-Editable plugin, that overrides global options in config (same as ``plugin_options`` for form field)   |
+------------+---------------+-----------+----------------------------------------------------------------------------------------------------------------------+
| attr       | object        | {}        | Attributes for link                                                                                                  |
+------------+---------------+-----------+----------------------------------------------------------------------------------------------------------------------+