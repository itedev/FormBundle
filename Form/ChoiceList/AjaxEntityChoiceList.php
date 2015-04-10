<?php

namespace ITE\FormBundle\Form\ChoiceList;

use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AjaxEntityChoiceList
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
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