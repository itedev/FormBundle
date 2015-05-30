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
    public function setFrom($from)
    {
        if ($from instanceof \DateTime) {
            $this->from = clone $from;
            $this->from->setTime(0, 0, 0);
        } else {
            $this->from = null;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setTo($to)
    {
        if ($to instanceof \DateTime) {
            $this->to = clone $to;
            $this->to->setTime(23, 59, 59);
        } else {
            $this->to = null;
        }

        return $this;
    }
}