Tinymce plugin
==============

Homepage
--------
http://www.tinymce.com/

Provided field types
--------------------
+--------------------------+---------------+-----------------------+
| Type                     | Parent type   | Required components   |
+==========================+===============+=======================+
| ite\_tinymce\_textarea   | textarea      | none                  |
+--------------------------+---------------+-----------------------+

Configuration
-------------
.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        plugins:
            tinymce:
                enabled:      true
                options:
                    script_url: '/bundles/acmedemo/js/tinymce/tinymce.min.js'
                    theme: modern
                    plugins: [ advlist, anchor, autolink, autoresize, autosave, charmap, code, contextmenu, directionality, emoticons, example, example_dependency, fullscreen, hr, image, insertdatetime, layer, legacyoutput, link, lists, media, nonbreaking, noneditable, pagebreak, paste, preview, print, save, searchreplace, spellchecker, tabfocus, table, template, textcolor, visualblocks, visualchars, wordcount ] # bbcode and fullpage are skipped