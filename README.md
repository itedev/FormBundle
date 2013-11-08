FormBundle
==========

Installation
------------

1. Add bundle to your project in composer.json:

```json
{
    "require": {
        "ite/form-bundle": "dev-master",
    }
}
```

2. Install bundle:

```
php composer.phar ite/form-bundle install
```

3. Add bundle to your app/AppKernel.php:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new ITE\JsBundle\ITEJsBundle(), // don't forget to enable ITEJsBundle!
        // ...
        new ITE\FormBundle\ITEFormBundle(),
        // ...
    );
}
```

Configuration
-------------
An example configuration is shown below:

```yml
# app/config/config.yml
ite_form:
    plugins:
        plugin_name:            ~               # just enable plugin with empty options
        another_plugin_name:                    # enable plugin and set its global options
            enabled:            true
            options:            {}              # global plugin options, which you can override in specific field
    timezone:                   Europe/London
```
List of javascripts, that you need to include in your global template:

```twig
{# app/Resources/views/base.html.twig #}
{% javascripts
    '@AcmeDemoBundle/Resources/public/js/jquery.js'
    {# javascript libraries  #}
    '@ITEJsBundle/Resources/public/js/sf.js' {# don't forget to include js from ITEJsBundle! #}
    '@ITEFormBundle/Resources/public/js/sf.form.js'
    '@ITEFormBundle/Resources/public/js/collection.js' {# optional, include it if you want to use extended form collections #}
%}
<script type="text/javascript" src="{{ asset_url }}"></script>
{% endjavascripts %}
{{ ite_js_sf_dump() }} {# this function dumps all needed data for SF object in ONE inline js #}
```
Internally some more javascripts are appended automatically in javascripts tag for each enabled plugin, it looks like this: '@ITEFormBundle/Resources/public/js/plugins/sf.plugin_name.js'.

Form field types and other plugin services are loaded ONLY if plugin enabled in config.

SF object extension
-------------------
This bundle add new field to SF object: SF.elements. To apply plugins on all elements on the page you need to call 

```js
SF.elements.apply();
```
function. You can pass context (http://api.jquery.com/jQuery/#jQuery-selector-context) as first argument, for applying plugins only inside specific element. Also you can pass object as a second parameter, that looks like this:

```js
{
  '__name__': 1,
  '__another_name__': 'abc'
}
```


If you need to change plugin options, which you cannot change via 'plugin_options' in PHP (i.e. callbacks,

```js
$('selector').on('apply.element.ite-form', function(e, elementData) {
  var $this = $(this);

  elementData.options = $.extend(true, elementData.options, {
    // extend plugin options
  });
});
```

Collections
-----------

For using advanced collections, you need to add additional js in your template:
```twig
{# app/Resources/views/base.html.twig #}
{% javascripts
    '@ITEFormBundle/Resources/public/js/collection.js'
%}
<script type="text/javascript" src="{{ asset_url }}"></script>
{% endjavascripts %}
```
...

Plugins
-------

<h4>Select2</h4>

Homepage: http://ivaynberg.github.io/select2/

Provided field types:

 * ite_select2_choice (inherits choice type)
 * ite_select2_language (inherits language type)
 * ite_select2_country (inherits country type)
 * ite_select2_timezone (inherits timezone type)
 * ite_select2_locale (inherits locale type)
 * ite_select2_currency (inherits currency type)
 * ite_select2_entity (inherits entity type)
 * ite_select2_document (inherits document type)
 * ite_select2_model (inherits model type)
 * ite_select2_ajax_entity (this type does not load all entities at once and use AJAX autocomplete instead of it)
 * ite_select2_google_font (inherits choice type) - test

Example configuration:

```yml
# app/config/config.yml
ite_form:
    plugins:
        select2:    ~
```

Usage:

```php
->add('entity', 'ite_select2_entity', array(
    'class' => 'AcmeDemoBundle:Foo',
    'property' => 'bar',
    'plugin_options' => array( // these options go directly javascript when plugin will be initialized
        'placeholder' => 'Type smth...',
        'minimumInputLength' => 2,
    )
))
```

```php
->add('entity', 'ite_select2_ajax_entity', array(
    'class' => 'AcmeDemoBundle:Foo',
    'property' => 'bar',
    'route' => 'acme_demo_foo_search', // route for searching Foo records by given query
    'route_parameters' => array(), // optional
    // 'allow_create' => true,
    // 'create_route' => 'acme_demo_ajax_foo_create', // route for creating Foo entity using given query
    'plugin_options' => array(
        'placeholder' => 'Type smth...',
        'minimumInputLength' => 2,
    ),
))
```

```php
// /src/Acme/DemoBundle/Controller/FooController.php

use FOS\RestBundle\Controller\Annotations\View;

class FooController extends Controller
{
    /**
     * @Route("/search", name="acme_demo_foo_search")
     * @View()
     */
    public function searchAction(Request $request)
    {
        $term = $request->query->get('term');

        // get an array of entities
        $result = $this->em->getRepository('AcmeDemoBundle:Foo')->yourSearchMethod($query);

        // 'property' value will be taken from corresponding value of field definition,
        // but you can set it obviously via second parameter of convertEntitiesToOptions()
        return $this->get('ite_form.select2.entity_converter')->convertEntitiesToOptions($result);
    }
    ...
}
```

<h4>Tinymce</h4>

Homepage: http://www.tinymce.com/

Provided field types:

* ite_tinymce_textarea (inherits textarea type)

Example configuration:

```yml
ite_form:
    plugins:
        tinymce:
            enabled:      true
            options:      
                script_url: '/bundles/acmedemo/js/tinymce/tinymce.min.js'
                theme: modern
                plugins:
                    - advlist
                    - anchor
                    - autolink
                    - autoresize
                    - autosave
                    - bbcode
                    - charmap
                    - code
                    - contextmenu
                    - directionality
                    - emoticons
                    - example
                    - example_dependency
                    - fullscreen
                    - hr
                    - image
                    - insertdatetime
                    - layer
                    - legacyoutput
                    - link
                    - lists
                    - media
                    - nonbreaking
                    - noneditable
                    - pagebreak
                    - paste
                    - preview
                    - print
                    - save
                    - searchreplace
                    - spellchecker
                    - tabfocus
                    - table
                    - template
                    - textcolor
                    - visualblocks
                    - visualchars
                    - wordcount
```

Usage:

```php
->add('textarea', 'ite_tinymce_textarea', array(
    'plugin_options' => array()
))
```

<h4>Bootstrap DateTimePicker (by smalot)</h4>

Homepage: http://www.malot.fr/bootstrap-datetimepicker/

Provided field types:

* ite_bootstrap_datetimepicker_datetime (inherits datetime type)
* ite_bootstrap_datetimepicker_date (inherits date type)
* ite_bootstrap_datetimepicker_time (inherits time type)
* ite_bootstrap_datetimepicker_birthday (inherits ite_bootstrap_datetimepicker_date type)

<h4>Bootstrap DateTimePicker2 (by tarruda)</h4>

Homepage: http://tarruda.github.io/bootstrap-datetimepicker/

Provided field types:

* ite_bootstrap_datetimepicker2_datetime (inherits datetime type)
* ite_bootstrap_datetimepicker2_date (inherits date type)
* ite_bootstrap_datetimepicker2_time (inherits time type)
* ite_bootstrap_datetimepicker2_birthday (inherits ite_bootstrap_datetimepicker2_date type)

<h4>Bootstrap ColorPicker</h4>

Homepage: http://www.eyecon.ro/bootstrap-colorpicker/

Provided field types:

* ite_bootstrap_colorpicker (inherits text type)

FormBuilder
-----------
Two new methods are added to FormBuilder:
```php
public function replaceType($name, $type); // change type for existing field

public function replaceOptions($name, $options); // change options for existing field
```
Field order
-----------

Debug
-----
If you want to dump huge objects in twig, and you cannot do it with default `dump()` function, you can use next way. Start listen for xDebug connections and add next construction to your twig template:

```twig
{% do ite_debug() %} {# for all variables in context #}
```
or

```twig
{% do ite_debug(form, entity) %} {# for specific variables #}
```
xDebug will automatically emits a breakpoint to the debug client on the specific line, and you can check your variable values in `$variables` var.
