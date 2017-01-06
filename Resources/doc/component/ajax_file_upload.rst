AJAX file upload
~~~~~~~~~~~~~~~~

This component allows to automate process of AJAX file uploading. This component use in combination with AJAX file
upload field types. Key features:

- AJAX file upload field types works as **usual ``file`` field type**. It means that you can upload file via AJAX and
  it will be mapped in the corresponding field of form data at form submit as an ``UploadedFile`` object.
- it works for create forms (when ``id`` for entity is not generated yet)
- it saves uploaded files when you submit form several times (for example if previous submit contains errors)
- it works for dynamically created collection items

This component add new option to form type called ``ajax_token``. When you want to use AJAX file upload field types -
you need to enable this option in your **root** form. It will generate random value when form is generated at the first
time and pass it through all form submits (as input hidden). This value is required for association form submit flow
for specific user and uploaded files.

Example configuration:

.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        components:
            ajax_file_upload:
                enabled: true
                tmp_prefix: uploads/tmp

All files uploaded via AJAX will be saved in tmp\_prefix directory
(relative to your web directory).

.. code-block:: php

    // src/Acme/DemoBundle/Form/Type/FooType.php

    class FooType extends AbstractType
    {
        public function setDefaultOptions(OptionsResolverInterface $resolver)
        {
            $resolver->setDefaults([
                'ajax_token' => true,
            ]);
        }
    }

To clean unprocessed uploaded files, you need to add next command in
your cron:

.. code-block:: bash

    php app/console ite:form:clear-temp-dir [minutes=60]

This command will remove files and files created more than 60 minutes ago (by default).

.. note ::
    use must run this command on behalf of the user which has permission to delete files created by Apache (or another
    web server user).