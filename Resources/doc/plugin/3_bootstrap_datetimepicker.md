### Bootstrap DateTimePicker (by smalot)

Homepage: http://www.malot.fr/bootstrap-datetimepicker/

Provided field types:

| Type                                  | Parent type                       | Required components |
|---------------------------------------|-----------------------------------|---------------------|
| ite_bootstrap_datetimepicker_datetime | datetime                          | none                |
| ite_bootstrap_datetimepicker_date     | date                              | none                |
| ite_bootstrap_datetimepicker_time     | time                              | none                |
| ite_bootstrap_datetimepicker_birthday | ite_bootstrap_datetimepicker_date | none                |

Example configuration:

```yml
# app/config/config.yml

ite_form:
    plugins:
        bootstrap_datetimepicker: ~
```