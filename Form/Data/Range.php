<?php

namespace ITE\FormBundle\Form\Data;

/**
 * Class Range
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class Range implements RangeInterface
{
    /**
     * @var mixed
     */
    protected $from;

    /**
     * @var mixed
     */
    protected $to;

    /**
     * @param mixed|null $from
     * @param mixed|null $to
     */
    public function __construct($from = null, $to = null)
    {
        $this
            ->setFrom($from)
            ->setTo($to)
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFrom()
    {
        return null !== $this->from;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * {@inheritdoc}
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasTo()
    {
        return null !== $this->to;
    }

    /**
     * {@inheritdoc}
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * {@inheritdoc}
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

}