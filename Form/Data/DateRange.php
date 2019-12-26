<?php

namespace ITE\FormBundle\Form\Data;

/**
 * Class DateRange
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DateRange extends Range
{
    /**
     * {@inheritdoc}
     */
    public function getFrom($raw = false)
    {
        if (!$raw) {
            $from = clone $this->from;
            $from->setTime(0, 0, 0);

            return $from;
        }

        return $this->from;
    }

    /**
     * {@inheritdoc}
     */
    public function getTo($raw = false)
    {
        if (!$raw) {
            $to = clone $this->to;
            $to->setTime(23, 59, 59);

            return $to;
        }

        return $this->to;
    }
}
