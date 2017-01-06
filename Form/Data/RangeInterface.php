<?php

namespace ITE\FormBundle\Form\Data;

/**
 * Interface RangeInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface RangeInterface
{
    /**
     * Has from
     *
     * @return bool
     */
    public function hasFrom();

    /**
     * Get from
     *
     * @return mixed
     */
    public function getFrom();

    /**
     * Set from
     *
     * @param mixed $from
     * @return $this
     */
    public function setFrom($from);

    /**
     * Has to
     *
     * @return bool
     */
    public function hasTo();

    /**
     * Get to
     *
     * @return mixed
     */
    public function getTo();

    /**
     * Set to
     *
     * @param mixed $to
     * @return $this
     */
    public function setTo($to);
}
