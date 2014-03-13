Bootstrap DateTimePicker2 (by tarruda) plugin
=============================================

Homepage
--------
http://tarruda.github.io/bootstrap-datetimepicker/

Provided field types
--------------------
+---------------------------------------------+-----------------------------------------+-----------------------+
| Type                                        | Parent type                             | Required components   |
+=============================================+=========================================+=======================+
| ite\_bootstrap\_datetimepicker2\_datetime   | datetime                                | none                  |
+---------------------------------------------+-----------------------------------------+-----------------------+
| ite\_bootstrap\_datetimepicker2\_date       | date                                    | none                  |
+---------------------------------------------+-----------------------------------------+-----------------------+
| ite\_bootstrap\_datetimepicker2\_time       | time                                    | none                  |
+---------------------------------------------+-----------------------------------------+-----------------------+
| ite\_bootstrap\_datetimepicker2\_birthday   | ite\_bootstrap\_datetimepicker2\_date   | none                  |
+---------------------------------------------+-----------------------------------------+-----------------------+

Configuration
-------------
.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        plugins:
            bootstrap_datetimepicker2: ~