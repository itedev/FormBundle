<?php

namespace ITE\FormBundle\Form\DataTransformer;

use ITE\Common\Util\ArrayUtils;
use ITE\Common\Util\DateTimeUtils;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\DataTransformer\BaseDateTimeTransformer;

/**
 * Class DatetimeToDateAndTimeTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DatetimeToDateAndTimeTransformer extends BaseDateTimeTransformer
{
    /**
     * @var string $dateName
     */
    private $dateName;

    /**
     * @var string $timeName
     */
    private $timeName;

    /**
     * @param string $dateName
     * @param string $timeName
     * @param string $inputTimezone
     * @param string $outputTimezone
     */
    public function __construct(
        $dateName,
        $timeName,
        $inputTimezone,
        $outputTimezone
    ) {
        parent::__construct($inputTimezone, $outputTimezone);

        $this->dateName = $dateName;
        $this->timeName = $timeName;
    }

    /**
     * @param string $value
     * @return string
     */
    public function transform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!$value instanceof \DateTime && !$value instanceof \DateTimeInterface) {
            throw new TransformationFailedException('Expected a \DateTime or \DateTimeInterface.');
        }

        $date = \DateTime::createFromFormat('Y-m-d', $value->format('Y-m-d'), new \DateTimeZone($this->inputTimezone));
        $time = \DateTime::createFromFormat('H:i:s|', $value->format('H:i:s'), new \DateTimeZone($this->inputTimezone));

        return [
            $this->dateName => $date,
            $this->timeName => $time,
        ];
    }

    /**
     * @param string $value
     * @return array
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return null;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $date = ArrayUtils::getValue($value, $this->dateName);
        $time = ArrayUtils::getValue($value, $this->timeName);

        return DateTimeUtils::createFromDateAndTime($date, $time, $this->inputTimezone, $this->outputTimezone);
    }
}
