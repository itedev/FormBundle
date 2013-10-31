<?php
//
//namespace ITE\FormBundle\Form\Core\Type;
//
//use Symfony\Component\Form\AbstractType;
//use ITE\FormBundle\Form\DataTransformer\DateRangeToLocalizedStringTransformer;
//use Symfony\Component\Form\Extension\Core\Type\DateType;
//use Symfony\Component\Form\FormBuilderInterface;
//use Symfony\Component\Form\FormInterface;
//use Symfony\Component\Form\FormView;
//use Symfony\Component\OptionsResolver\OptionsResolverInterface;
//
//class DateRangeType extends AbstractType
//{
//    public function setDefaultOptions(OptionsResolverInterface $resolver)
//    {
//        $resolver->setDefaults(array(
//            'widget' => 'single_text',
//            'input' => 'datetime',
//        ));
//    }
//
//    public function buildForm(FormBuilderInterface $builder, array $options)
//    {
//        parent::buildForm($builder, $options);
//
//        $dateFormat = is_int($options['format']) ? $options['format'] : DateType::DEFAULT_FORMAT;
//        $timeFormat = \IntlDateFormatter::NONE;
//        $calendar = \IntlDateFormatter::GREGORIAN;
//        $pattern = is_string($options['format']) ? $options['format'] : null;
//        $builder->resetViewTransformers();
//        $builder->addViewTransformer(new DateRangeToLocalizedStringTransformer(
//            $options['model_timezone'],
//            $options['view_timezone'],
//            $dateFormat,
//            $timeFormat,
//            $calendar,
//            $pattern
//        ));
//    }
//
//    public function getParent()
//    {
//        return 'date';
//    }
//
//    public function getName()
//    {
//        return 'ite_bootstrap_daterange';
//    }
//}