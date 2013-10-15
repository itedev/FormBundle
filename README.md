FormBundle
==========

Configuration
-------------
```yml
# app/config/config.yml
ite_form:
    plugins:
        select2:          ~     # just enable plugin with default settings
        tinymce:
            enabled:      true
            options:      {}    # you can set also default plugin settings, which you can override for in specific field
    timezone:             Asia/Omsk
```   
Form field types and other plugin services are loaded ONLY if plugin enabled in config.

```twig
{# app/Resources/views/base.html.twig #}

{% javascripts
    '@ITEJsBundle/Resources/public/js/sf.js'
    ...
    {# javascript libraries  #}
    ...
    '@ITEFormBundle/Resources/public/js/sf.form.js'
%}
<script type="text/javascript" src="{{ asset_url }}"></script>
{% endjavascripts %}
{{ ite_js_sf_dump() }}
```
Internally some more javascripts are included automatically in javascripts tag for each enabled plugin, for example: '@ITEFormBundle/Resources/public/js/plugins/sf.select2.js'.

SF object extension
-------------------
This bundle add new field to SF object: SF.elements. To apply plugins on needed elements you need to call 
```js
SF.elements.apply();
```
function. If you are using plugin fields in collection field, you need to call this function after inserting html to DOM and pass prototype_name and collection item index to it:
```js
SF.elements.apply({'__name__': 1});
```
And event if you are using collection in collection, you can do so:
```js
SF.elements.apply({'__name__': 1, '__another_name__': 2});
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

Examples:

```php
->add('entity', 'ite_select2_entity', array(
    'class' => 'AcmeDemoBundle:Foo',
    'plugin_options' => array(          
        'placeholder' => 'Type smth...', // these options go directly javascript when plugin will be initialized 
        'minimumInputLength' => 2,
    )
))
```

```php
->add('fundingCode', 'ite_select2_ajax_entity', array(
    'class' => 'AcmeDemoBundle:Foo',
    'route' => 'acme_demo_ajax_foo_search', // route for searching Foo records by given query
    // 'allow_create' => true,
    // 'create_route' => 'acme_demo_ajax_foo_create', // route for creating Foo entity using given query
    'plugin_options' => array(
        'placeholder' => 'Type smth...',
        'minimumInputLength' => 2,
    ),
))
```

```php
// /src/Acme/DemoBundle/Controller/AjaxFooController.php

use FOS\RestBundle\Controller\Annotations\View;

class AjaxFooController extends Controller
{
    /**
     * @Route("/search", name="acme_demo_ajax_foo_search")
     * @View()
     */
    public function searchAction(Request $request)
    {
        $query = $request->query->get('q');

        $result = this->em->getRepository('AcmeDemoBundle:Foo')->search($query);

        return $this->get('ite_form.select2.converter')->convertEntitiesToOptions($result, $labelPath);
    }
    ...
}
```

<h4>Tinymce</h4>
Provide next field types:
* ite_tinymce_textarea

Configuration:

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
FormBuilder
-----------
Two new methods are added to FormBuilder:
```php
public function replaceType($name, $type); // change type for existing field

public function replaceOptions($name, $options); // change options for existing field
```
Field order
-----------

