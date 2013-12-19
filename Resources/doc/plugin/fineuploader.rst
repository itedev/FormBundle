Fine Uploader
~~~~~~~~~~~~~

Homepage: http://fineuploader.com/

Provided field types:

+---------------------------+-------------------+-----------------------+
| Type                      | Parent type       | Required components   |
+===========================+===================+=======================+
| ite\_fineuploader\_file   | ite\_ajax\_file   | ajax\_file\_upload    |
+---------------------------+-------------------+-----------------------+

Example configuration:

.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        plugins:
            fineuploader: ~