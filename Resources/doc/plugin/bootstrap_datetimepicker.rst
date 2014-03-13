Bootstrap DateTimePicker (by smalot) plugin
===========================================

Homepage
--------
http://www.malot.fr/bootstrap-datetimepicker/

Provided field types
--------------------
+--------------------------------------------+----------------------------------------+-----------------------+
| Type                                       | Parent type                            | Required components   |
+============================================+========================================+=======================+
| ite\_bootstrap\_datetimepicker\_datetime   | datetime                               | none                  |
+--------------------------------------------+----------------------------------------+-----------------------+
| ite\_bootstrap\_datetimepicker\_date       | date                                   | none                  |
+--------------------------------------------+----------------------------------------+-----------------------+
| ite\_bootstrap\_datetimepicker\_time       | time                                   | none                  |
+--------------------------------------------+----------------------------------------+-----------------------+
| ite\_bootstrap\_datetimepicker\_birthday   | ite\_bootstrap\_datetimepicker\_date   | none                  |
+--------------------------------------------+----------------------------------------+-----------------------+

Configuration
-------------
.. code-block:: yaml

    # app/config/config.yml

    ite_form:
        plugins:
            bootstrap_datetimepicker: ~