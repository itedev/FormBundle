<?php

namespace ITE\FormBundle\Form\DataMapper;

use ITE\Common\Util\ReflectionUtils;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\DataMapper\PropertyPathMapper as BasePropertyPathMapper;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class PropertyPathMapper
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class PropertyPathMapper extends BasePropertyPathMapper
{
    /**
     * {@inheritdoc}
     */
    public function mapDataToForms($data, $forms)
    {
        $empty = null === $data || array() === $data;

        if (!$empty && !is_array($data) && !is_object($data)) {
            throw new UnexpectedTypeException($data, 'object, array or empty');
        }

        foreach ($forms as $form) {
            $propertyPath = $form->getPropertyPath();
            $config = $form->getConfig();

            if (!$empty && null !== $propertyPath && $config->getMapped()) {
                try {
                    $form->setData($this->getPropertyAccessor()->getValue($data, $propertyPath));
                } catch (NoSuchPropertyException $e) {
                    $parents = $form->getConfig()->getOption('hierarchical_parents', []);
                    if (!empty($parents)) {
                        $form->setData($form->getConfig()->getData());
                    } else {
                        throw $e;
                    }
                }
            } else {
                $form->setData($form->getConfig()->getData());
            }
        }
    }

    /**
     * @return PropertyAccessorInterface
     */
    protected function getPropertyAccessor()
    {
        return ReflectionUtils::getValue($this, 'propertyAccessor');
    }
}
