FormBundle
==========

Configuration
-------------
```yml
ite_form:
    plugins:
        select2:          ~     # just enable plugin with default settings
        tinymce:
            enabled:      true
            options:      {}    # you can set also default plugin settings, which you can override for in specific field
    timezone:             Asia/Omsk
```    
Plugins
-------
<h4>Select2</h4>
Provide next field types:
 * ite_select2_choice
 * ite_select2_language
 * ite_select2_country
 * ite_select2_timezone
 * ite_select2_locale
 * ite_select2_currency
 * ite_select2_entity
 * ite_select2_ajax_entity (this type does not load all entities at once and use AJAX autocomplete instead of it)

```php
->add('entity', 'ite_select2_entity', array(
    ...
    'plugin_options' => array(          
        'placeholder' => 'Type smth...', // these options go directly javascript when plugin will be initialized 
        'minimumInputLength' => 2,
    )
))
```
```php
->add('fundingCode', 'ite_select2_ajax_entity', array(
    'class' => 'AcmeDemoBundle:Foo',
    'route' => 'acme_demo_ajax_foo_list', // route for loading Foo records by given query
    // 'allow_create' => true,
    // 'create_route' => 'acme_demo_ajax_foo_create', // route for creating Foo entity using given query
    'plugin_options' => array(
        'placeholder' => 'Type smth...',
        'minimumInputLength' => 2,
    ),
))
```
