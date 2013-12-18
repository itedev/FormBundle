### Bootstrap DateTimePicker2 (by tarruda)

Homepage: http://tarruda.github.io/bootstrap-datetimepicker/

Provided field types:

| Type                                   | Parent type                        | Required components |
|----------------------------------------|------------------------------------|---------------------|
| ite_bootstrap_datetimepicker2_datetime | datetime                           | none                |
| ite_bootstrap_datetimepicker2_date     | date                               | none                |
| ite_bootstrap_datetimepicker2_time     | time                               | none                |
| ite_bootstrap_datetimepicker2_birthday | ite_bootstrap_datetimepicker2_date | none                |

Example configuration:

```yml
# app/config/config.yml

ite_form:
    plugins:
        bootstrap_datetimepicker2: ~
```