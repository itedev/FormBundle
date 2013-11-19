ITEFormBundle
=============

ITEFormBundle adds a lot of cool features to Symfony 2 forms, such as: new field types and extensions, integration with popular JavaScript libraries and jQuery plugins, improved collection field, automatic AJAX file upload handling, dynamic choice fields, etc.

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

This bundle has modular structure, it means that most services are disabled (not loaded) by default and you can choose - what features you need and enable only it. There are two important items in the config: components and plugins. **Component** is a set of services that implements specific feature. **Plugin** is a set of new form field types that use specific JavaScript library or jQuery plugin. Some plugins require enable specific component.

An example configuration is shown below:

```yml
# app/config/config.yml
ite_form:
    components:
        component_name:         ~               # just enable component
        another_component_name:                 # enabled component and set some options for it
            enabled:            true
            option_name:        option_value
    plugins:
        plugin_name:            ~               # just enable plugin with empty options
        another_plugin_name:                    # enable plugin and set its global options
            enabled:            true
            options:            {}              # global plugin options, which you can override in specific field
```
List of javascripts, that you need to include in your global template:

```twig
{# app/Resources/views/base.html.twig #}
{% javascripts
    '@AcmeDemoBundle/Resources/public/js/jquery.js'
    {# javascript libraries  #}
    '@ITEJsBundle/Resources/public/js/sf.js' {# don't forget to include this js from ITEJsBundle! #}
    '@ITEFormBundle/Resources/public/js/sf.form.js'
    '@ITEFormBundle/Resources/public/js/collection.js' {# optional, include it if you want to use improved form collections #}
%}
<script type="text/javascript" src="{{ asset_url }}"></script>
{% endjavascripts %}
{{ ite_js_sf_dump() }} {# this function dumps all needed data for SF object in ONE inline js #}
```

Internally some more javascripts are appended automatically in `javascripts` tag for each enabled plugin, it looks like this: '@ITEFormBundle/Resources/public/js/plugins/sf.plugin_name.js'.

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

If you need to change plugin options, which you cannot change via 'plugin_options' in PHP (i.e. callbacks, regexps, dates, etc), you can add next event listener:

```js
$('selector').on('apply.element.ite-form', function(e, elementData) {
  var $this = $(this);

  elementData.options = $.extend(true, elementData.options, {
    // extend plugin options
  });
});
```

It will be called right before plugin will be applied.

Collections
-----------

This bundle greatly improves Symfony 2 collections (and collections from MopaBootstrapBundle):
 * all field plugins **automatically** applied when new collection item is added **(and it works even for collections inside collection, collection inside collection inside collection, and so on)**
 * new options, callbacks and events are added to form collections, that make work with it much easier
 * you can set global or per collection options
 * theming of collections become more flexible, you can render it as a table, list or anything you want

For using it, you need to add additional js in your template:

```twig
{# app/Resources/views/base.html.twig #}
{% javascripts
    '@ITEFormBundle/Resources/public/js/collection.js'
    {# and remove '@MopaBootstrapBundle/Resources/public/js/mopabootstrap-collection.js' if you have it #}
%}
<script type="text/javascript" src="{{ asset_url }}"></script>
{% endjavascripts %}
```

Several new options are added to the collection field:

Option | Default | Description
--- | --- | ---
collection_item_tag | 'div' | Root tag for collection item
collection_id | By default it equals to field `unique_block_prefix` | Value of attribute `data-collection-id` that will be added to all collection instances of specific type. If you are using collection inside collection, it will be the same for all children collections. Also you can always get collection element for any inner element, by calling: `$(element).closest('[data-collection-id]')`

Global settings for collections are defined in `$.fn.collection.defaults`:

Option | Default | Description
--- | --- | ---
beforeAdd | function(item, collection) {} | Function that will be called before new collection item will be added. If return `false` item will not be added.
onAdd | function(item, collection) {} | Function that will be called after new collection item was added.
beforeRemove | function(item, collection) {} | Function that will be called before collection item will be removed. If return `false` item will not be removed.
onRemove | function(item, collection) {} | Function that will be called after collection item was deleted.
show | {type: 'show', length: 0} | Animation for showing new collection items. Available values for type: 'slide', 'fade', 'show'. Length is integer in ms.
hide | {type: 'hide', length: 0} | Animation for hiding collection items. Available values for type: 'slide', 'fade', 'hide'. Length is integer in ms.

You can override it in this way:

```js
$.fn.collection.defaults = {
  show: {
    type: 'slide',
    length: 100
  },
  hide: {
    type: 'fade',
    length: 200
  }
};
```

If you want to use another options for specific collection, you can override it in this way:

```js
$.fn.collection.collections.%collection_id% = {
 beforeAdd: function(item, collection) {
   // ...
 }
};
```

Also plugin trigger two events:
 * add.collection.ite-form - triggered after new collection item was added
 * remove.collection.ite-form - triggered after collection item was removed

Example of usage:

```js
// for root collections
$('collection_selector').on('add.ite-collection remove.collection.ite-form', function(e, item) {
  var collection = $(this);

  $('#collection_count').html(collection.collection('itemsCount'));
});

// for children collections
$('root_collection_selector').on('add.ite-collection remove.collection.ite-form', 'child_collection_selector', function(e, item) {
  var collection = $(this);

  $('#child_collection_count').html(collection.collection('itemsCount'));

  e.stopPropagation(); // stop event propagation to parent collections
});
```

**Note**: for root collection selector you can anything you like: `#id`, `.class` or `[data-collection-id="..."]`. For children collections it is easier to use `[data-collection-id="..."]` selector (or class if you added it).

Collection plugin methods:

Method | Arguments | Description
--- | --- | ---
add | None | Add new collection item. It is automatically called when you click on element with `data-collection-add-btn` attribute.
remove | Any $(element) inside collection item | Remove corresponding collection item.
items | None | Return jQuery collection of all collection items.
itemsCount | None | Return number of collection items.
parents | None | Return all parent collection (if exist) (parent elements with `data-collection-id` attribute).
parentsCount | None | Return number of parent collections.
hasParent | None | Return `true` if collection has parent collection, `false` otherwise.
itemsWrapper | None | Return collection items wrapper jQuery object (with class `collection-items`).

Example of using usual collection:

```php
// FooType.php
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
```

```twig
{# foo.html.twig #}
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
```

Example of using collection that looks like tables:

```php
// FooType.php
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
```

```twig
{# foo.html.twig #}
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
```

You can render add and remove button manually, if you will follow next instructions:
 * add button: add `data-collection-add-btn` attribute and place it inside collection element (element with 'data-collection-id' attribute).
 * remove button: add `data-collection-remove-btn` attribute and place it inside collection item element (element with '.collection-item' class).

Components
----------

There are list of supported components:
 * dynamic_choice
 * ajax_file_upload

### Dynamic choice

This component allows to modify submitted options of `choice` (and all child field types including `entity`). It adds new option `allow_modify` to `choice` field type. If it is set to `true` - it means that you can change options in JavaScript (add new option and submit it, or completely replace list of options and submit one from new list) and submit it without errors. It works for all combinations of `multiple` and `expanded` options. You don't need to use `PRE_SUBMIT` event to re-add same field with submitted options (as you did before).

**Note:** new options are not added to the option list, when form is rendered after submit (successful or not). It means that value from new option will be saved, but will not be added to the option list.
**Note:** don't forget about validation when use `allow_modify` option!

Usage:

```php
// src/Acme/DemoBundle/Form/Type/FooType.php

public function buildForm(FormBuilderInterface $builder, array $options)
{
    $builder
        ->add('bar', 'choice', array(
            'allow_modify' => true,
        ))
        // ...
    ;
}
```

### AJAX file upload

This component allows to automate process of AJAX file uploading. This component use in combination with AJAX file upload field types. Key features:
 * AJAX file upload field types works as **usual `file` field type**. It means that you can upload file via AJAX and it will be mapped in the corresponding field of form data as an `UploadedFile` object.
 * it works for create forms (when `id` for entity is not generated yet)
 * it saves uploaded files when you submit form several times (for example if previous submit contains errors)
 * it works for dynamically created collection items

This component add new option to form type called `ajax_token`. When you want to use AJAX file upload field types - you need to enable this option in your **root** form. It will generate random value when form is generated at the first time and pass it through all form submits (as input hidden). This value is required for association form submit flow for specific user and uploaded files.

Example configuration:

```yml
ite_form:
    components:
        ajax_file_upload:
            enabled: true
            tmp_prefix: uploads/tmp
```
All files uploaded via AJAX will be saved in tmp_prefix directory (relative to your web directory).

```php
// src/Acme/DemoBundle/Form/Type/FooType.php

public function setDefaultOptions(OptionsResolverInterface $resolver)
{
    $resolver->setDefaults(array(
        'ajax_token' => true,
    ));
}
```

To clean unprocessed uploaded files, you need to add next command in your cron:
```
php app/console ite:form:clear-temp-dir [minutes=60]
```

This command will remove files and files created more than 60 minutes ago (by default).

**Note:** use must run this command on behalf of the user which has permission to delete files created by Apache (or another web server user).

Plugins
-------

There are list of supported plugins:
 * select2
 * tinymce
 * bootstrap_colorpicker
 * bootstrap_datetimepicker
 * bootstrap_datetimepicker2
 * fileupload
 * fineuploader
 * form (WIP)

### Select2

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
 * ite_select2_ajax_entity (inherits ite_ajax_entity type)
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

Few words about `ite_select2_ajax_entity`. This type does not load all entities at once and use AJAX autocomplete instead of it.

Usage:

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
        $result = $this->em->getRepository('AcmeDemoBundle:Foo')->yourSearchMethod($term);

        // 'property' value will be taken from corresponding value of field definition,
        // but you can set it obviously via second parameter of convertEntitiesToOptions()
        return $this->get('ite_form.select2.entity_converter')->convertEntitiesToOptions($result);
    }
}
```

**Note:** it's strongly recommended to set obviously property as a second parameter in `convertEntitiesToOptions` method.

### Tinymce

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
                plugins: [ advlist, anchor, autolink, autoresize, autosave, charmap, code, contextmenu, directionality, emoticons, example, example_dependency, fullscreen, hr, image, insertdatetime, layer, legacyoutput, link, lists, media, nonbreaking, noneditable, pagebreak, paste, preview, print, save, searchreplace, spellchecker, tabfocus, table, template, textcolor, visualblocks, visualchars, wordcount ] # bbcode and fullpage are skipped
```

Usage:

```php
->add('textarea', 'ite_tinymce_textarea', array(
    'plugin_options' => array()
))
```

### Bootstrap DateTimePicker (by smalot)

Homepage: http://www.malot.fr/bootstrap-datetimepicker/

Provided field types:

* ite_bootstrap_datetimepicker_datetime (inherits datetime type)
* ite_bootstrap_datetimepicker_date (inherits date type)
* ite_bootstrap_datetimepicker_time (inherits time type)
* ite_bootstrap_datetimepicker_birthday (inherits ite_bootstrap_datetimepicker_date type)

### Bootstrap DateTimePicker2 (by tarruda)

Homepage: http://tarruda.github.io/bootstrap-datetimepicker/

Provided field types:

* ite_bootstrap_datetimepicker2_datetime (inherits datetime type)
* ite_bootstrap_datetimepicker2_date (inherits date type)
* ite_bootstrap_datetimepicker2_time (inherits time type)
* ite_bootstrap_datetimepicker2_birthday (inherits ite_bootstrap_datetimepicker2_date type)

### Bootstrap ColorPicker

Homepage: http://www.eyecon.ro/bootstrap-colorpicker/

Provided field types:

* ite_bootstrap_colorpicker (inherits text type)

### File Upload

Homepage: http://blueimp.github.io/jQuery-File-Upload/

Provided field types:

* ite_fileupload_file (inherits ite_ajax_file type)

### Fine Uploader

Homepage: http://fineuploader.com/

Provided field types:

* ite_fineuploader_file (inherits ite_ajax_file type)

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
