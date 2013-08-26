daterange
=========

$builder
    ->add('dateRange', 'daterange', array(
        'model_timezone' => $options['data_timezone'], // optional
        'view_timezone' => $options['user_timezone'], // optional
    ))

$dateRange = $form['dateRange']->getData()
$dateRange[0]->format('Y-m-d H:i:s')
$dateRange[1]->format('Y-m-d H:i:s')

------------------------------------------------------------------------------------------------------------------------

datetime, date and time
=======================

$builder
    ->add('date', 'ite_bootstrap_date_picker', array(
        'format' => 'dd/MM/yyyy', // optional
    ))
    ->add('time', 'ite_bootstrap_time_picker', array(
        'model_timezone' => $options['data_timezone'], // optional
        'view_timezone' => $options['user_timezone'], // optional
    ))
    ->add('datetime', 'ite_bootstrap_datetime_picker', array(
        'format' => 'yyyy-MM-dd HH:mm:ss',
        'model_timezone' => $options['data_timezone'], // optional
        'view_timezone' => $options['user_timezone'], // optional
    ))

https://matt.drollette.com/2012/07/user-specific-timezones-with-symfony2-and-twig-extensions/
http://stackoverflow.com/questions/10694315/symfony2-where-to-set-a-user-defined-time-zone
------------------------------------------------------------------------------------------------------------------------

select2 (choice, entity, ajax entity)
=====================================

$builder
    ->add('choice', 'ite_select2_choice', array(
        'choices' => array(),
    ))
    ->add('entity', 'ite_select2_entity', array(
        'class' => 'class_name',
    ))
    ->add('ajaxEntity', 'ite_select2_ajax_entity', array(
        'class' => 'class_name',
        'route' => 'route_name',
        'route_parameters' => array(), // optional
    ))