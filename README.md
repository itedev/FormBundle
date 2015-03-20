ITEFormBundle
=============

ITEFormBundle adds a lot of cool features to Symfony 2 forms, such as: new field types and extensions, integration with popular JavaScript libraries and jQuery plugins, improved collection field, hierarchical selects (and other fields!), automatic AJAX file upload handling, dynamic choice fields, etc.

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
            new ITE\FormBundle\ITEFormBundle(),
            // ...
        );
    }
    ```

Configuration
-------------

This bundle has modular structure, it means that most services (and javascript files required for it) are disabled (not loaded) by default and you can choose - what features you need and enable only it. There are two important items in the config: components and plugins. **Component** is a set of services that implements specific feature. **Plugin** is a set of new form field types that use specific JavaScript library or jQuery plugin. Some plugins require enable specific component. JavaScript files required for specific enabled component or plugin will be automatically added into your assets list, you don't need to add it manually when you enabled or disable some feature (just don't forget to clear the cache after that).

An example configuration is shown below:

```yml
# app/config/config.yml

ite_form:
    components:
        component_name:         ~               # just enable component
        another_component_name:                 # enabled component and set options for it
            enabled:            true
            option_name:        option_value
    plugins:
        plugin_name:            ~               # just enable plugin with empty options
        another_plugin_name:                    # enable plugin and set its global options
            enabled:            true
            options:                            # global plugin options, which you can override in specific field
                option_name:    option_value

```
List of JavaScript files, that you need to include in your global template:

```twig
{# app/Resources/views/base.html.twig #}

{% stylesheets
    {# ... #}
    ite_js_sf_assets()
%}
<link href="{{ asset_url }}" type="text/css" rel="stylesheet" media="screen" />
{% endstylesheets %}

{% javascripts
    '@AcmeDemoBundle/Resources/public/js/jquery.js'
    {# ... #}
    ite_js_sf_assets()
%}
<script type="text/javascript" src="{{ asset_url }}"></script>
{% endjavascripts %}

{{ ite_js_sf_dump() }} {# this function dumps all needed data for SF object in ONE inline js #}
```

Components
----------

There are list of supported components:
 * collection
 * dynamic_choice
 * hierarchical
 * ajax_file_upload
 * editable
 * ordered
 * validation
 * debug

Plugins
-------

There are list of supported plugins:
 * select2
 * tinymce
 * bootstrap_colorpicker
 * bootstrap_spinedit
 * bootstrap_datetimepicker
 * bootstrap_datetimepicker2
 * fileupload
 * fineuploader
 * minicolors
 * knob
 * starrating
 * x_editable
 * nod
 * parsley
 * form

You can change options for specific plugin field in several ways:

First of all, you can change global options for specific plugin in your app/config/config.yml:

```yml
# app/config/config.yml

ite_form:
    plugins:
        plugin_name:
            enabled:            true
            options:
                option_name:    option_value
```

Secondly, you can override global options in specific field in `plugin_options` option:

```php
// src/Acme/DemoBundle/Form/Type/FooType.php

class FooType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bar', null, array(
                'plugin_options' => array(
                    'option_name' => 'option_value',
                ),
            ))
        ;
    }
}
```

Thirdly, you can change plugin options in JavaScript using `ite-before-apply.plugin` event listener. It will be also helpful, if you need to change plugin options, that you cannot change via 'plugin_options' in PHP (i.e. callbacks, regexps, dates, etc):

```js
$('selector').on('ite-before-apply.plugin', function(e, data, plugin) {
  var $this = $(this);

  data.options = $.extend(true, data.options, {
    // extend plugin options
  });

  // return false; // if you return false - plugin will not be applied
});
```

Also there are `ite-apply.plugin` event, that will be called right after plugin will be applied:

```js
$('selector').on('ite-apply.plugin', function(e, data, plugin) {
  var $this = $(this);

  // some actions after plugin is applied
});
```

SF object extension
-------------------

This bundle add new field to SF object: SF.elements. To apply plugins on all elements on the page you need to call `SF.elements.apply()` function.

**Note:** this method is automatically called inside `ite_js_sf_dump()` function.

You can pass context (http://api.jquery.com/jQuery/#jQuery-selector-context) as first argument, for applying plugins only inside specific element (i.e. content that was retrieved through AJAX). Also you can pass object as a second parameter, that looks like this:

```js
{
  '__name__': 1,
  '__another_name__': 2
}
```

When SF object will iterate through all its elements to apply plugins, it will replace keys from this object to corresponding values in element selectors. It is used internally in `@ITEFormBundle/Resources/public/js/collection.js` for replacing collection's *prototype_name* to collection item indexes.