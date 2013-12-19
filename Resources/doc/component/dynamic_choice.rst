Dynamic choice
~~~~~~~~~~~~~~

This component allows to modify submitted options of ``choice`` (and all child field types including ``entity``). It
adds new option ``allow_modify`` to ``choice`` field type. If it is set to ``true`` - it means that you can change
options in JavaScript (add new option and submit it, or completely replace list of options and submit one from new
list) and submit it without errors. It works for all combinations of ``multiple`` and ``expanded`` options. You don't
need to use ``PRE_SUBMIT`` event to re-add same field with submitted options (as you did before).

.. note ::
    new options are not added to the option list, when form is rendered after submit (successful or not). It means that
    value from new option will be saved, but will not be added to the option list.

.. note ::
    don't forget about validation when use ``allow_modify`` option!

Example configuration:

.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        components:
            dynamic_choice: ~

Usage:

.. code-block:: php

    // src/Acme/DemoBundle/Form/Type/FooType.php

    class FooType extends AbstractType
    {
        public function buildForm(FormBuilderInterface $builder, array $options)
        {
            $builder
                ->add('bar', 'choice', array(
                    'allow_modify' => true,
                ))
            ;
        }
    }