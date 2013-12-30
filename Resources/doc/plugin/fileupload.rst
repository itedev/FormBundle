File Upload plugin
==================

Homepage
--------
http://blueimp.github.io/jQuery-File-Upload/

Provided field types
--------------------
+-------------------------+-------------------+-----------------------+
| Type                    | Parent type       | Required components   |
+=========================+===================+=======================+
| ite\_fileupload\_file   | ite\_ajax\_file   | ajax\_file\_upload    |
+-------------------------+-------------------+-----------------------+

Configuration
-------------
.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        plugins:
            fileupload: ~