<?php

namespace ITE\FormBundle\Util;

use Symfony\Component\Form\Exception\StringCastException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class Select2Utils
{
    /**
     * @param $values
     * @param $idPath
     * @param null $labelPath
     * @return array
     */
    public static function convertToValues($values, $idPath, $labelPath = null)
    {
        /** @var $propertyAccessor PropertyAccessor */
        $propertyAccessor = PropertyAccess::createPropertyAccessor();

        return array_map(function($value) use ($propertyAccessor, $idPath, $labelPath) {
            if ($labelPath) {
                $label = $propertyAccessor->getValue($value, $labelPath);
            } elseif (is_object($value) && method_exists($value, '__toString')) {
                $label = (string) $value;
            } else {
                throw new StringCastException(sprintf('A "__toString()" method was not found on the objects of type "%s" passed to the choice field. To read a custom getter instead, set the argument $labelPath to the desired property path.', get_class($value)));
            }

            return array(
                'id' => $propertyAccessor->getValue($value, $idPath),
                'text' => $label
            );
        }, $values);
    }

}