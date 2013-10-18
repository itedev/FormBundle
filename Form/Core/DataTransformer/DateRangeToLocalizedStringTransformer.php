<?php
//
//namespace ITE\FormBundle\Form\Core\DataTransformer;
//
//use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToLocalizedStringTransformer as BaseDateTimeTransformer;
//use Symfony\Component\Form\Exception\TransformationFailedException;
//
//class DateRangeToLocalizedStringTransformer extends BaseDateTimeTransformer
//{
//    /**
//     * Transforms a normalized date into a localized date string/array.
//     *
//     * @param \DateTime $dateTime Normalized date.
//     *
//     * @return string|array Localized date string/array.
//     *
//     * @throws TransformationFailedException If the given value is not an instance
//     *                                       of \DateTime or if the date could not
//     *                                       be transformed.
//     */
//    public function transform($dateTime)
//    {
//        if (null === $dateTime) {
//            return '';
//        }
//
//        if (!is_array($dateTime) || 2 !== count($dateTime)) {
//            throw new TransformationFailedException('Expected an array.');
//        }
//
//        foreach ($dateTime as $index => $value) {
//            $dateTime[$index] = parent::transform($value);
//        }
//
//        return implode(' - ', $dateTime);
//    }
//
//    /**
//     * Transforms a localized date string/array into a normalized date.
//     *
//     * @param string|array $value Localized date string/array
//     *
//     * @return array Normalized dates array
//     *
//     * @throws TransformationFailedException if the given value is not a string,
//     *                                       if the date could not be parsed or
//     *                                       if the input timezone is not supported
//     */
//    public function reverseTransform($value)
//    {
//        if (!is_string($value)) {
//            throw new TransformationFailedException('Expected a string.');
//        }
//
//        if ('' === $value) {
//            return null;
//        }
//
//        $dateTime = explode(' - ', $value, 2);
//        foreach ($dateTime as $index => $value) {
//            $dateTime[$index] = parent::reverseTransform($value);
//        }
//        $dateTime[1]->setTime(23, 59, 59);
//
//        return $dateTime;
//    }
//
//}
