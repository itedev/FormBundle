<?php

namespace ITE\FormBundle\FormAccess;

/**
 * Class FormAccess
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
final class FormAccess
{
    /**
     * @return FormAccessorInterface
     */
    public static function createFormAccessor()
    {
        return new FormAccessor();
    }

    private function __construct()
    {
    }
}
