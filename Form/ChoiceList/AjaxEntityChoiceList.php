<?php

namespace ITE\FormBundle\Form\ChoiceList;

use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Component\Form\Exception\StringCastException;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Form\Exception\RuntimeException;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AjaxEntityChoiceList
 * @package ITE\FormBundle\Form\ChoiceList
 */
class AjaxEntityChoiceList extends EntityChoiceList
{
    /**
     * @param ObjectManager $manager
     * @param string $class
     * @param null $labelPath
     */
    public function __construct(ObjectManager $manager, $class, $labelPath = null)
    {
        parent::__construct($manager, $class, $labelPath, null, array());
    }

    /**
     * @param array $entities
     * @return array
     */
    public function getValuesForChoices(array $entities)
    {
        if (empty($entities) || (1 === count($entities) && !isset($entities[0]))) {
            return array();
        }

        parent::initialize($entities, array(), array());

        return parent::getValuesForChoices($entities);
    }

//    /**
//     * @param array $entities
//     */
//    public function addEntities($entities)
//    {
//        if (empty($entities)) {
//            return;
//        }
//        if (!is_array($entities) && !$entities instanceof \Traversable) {
//            $entities = array($entities);
//        }
//
//        parent::initialize($entities, array(), array());
//    }
}