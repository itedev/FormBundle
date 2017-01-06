<?php

namespace ITE\FormBundle\FormAccess;

/**
 * Interface FormPathInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface FormPathInterface
{
    /**
     * @return int
     */
    public function getLength();

    /**
     * @return array
     */
    public function getElements();

    /**
     * @return bool
     */
    public function isAbsolute();

    /**
     * @return bool
     */
    public function isRelative();

    /**
     * @param int $index
     * @return string
     */
    public function getElement($index);


    /**
     * @param int $index
     * @return bool
     */
    public function isParent($index);

    /**
     * @return string
     */
    public function __toString();
}
