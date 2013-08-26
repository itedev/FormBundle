<?php

namespace ITE\FormBundle\Form\Doctrine\ChoiceList;

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\Extension\Core\ChoiceList\ObjectChoiceList;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Form\Exception\RuntimeException;
use Doctrine\Common\Persistence\ObjectManager;

class AjaxEntityChoiceList extends ObjectChoiceList
{
    /**
     * The identifier field, if the identifier is not composite
     *
     * @var array
     */
    private $idField = null;

    /**
     * Whether to use the identifier for index generation
     *
     * @var Boolean
     */
    private $idAsIndex = false;

    /**
     * Whether to use the identifier for value generation
     *
     * @var Boolean
     */
    private $idAsValue = false;

    /**
     * Creates a new entity choice list.
     *
     * @param ObjectManager             $manager           An EntityManager instance
     * @param string                    $class             The class name
     * @param string                    $labelPath         The property path used for the label
     * @param array                     $entities          An array of choices
     * @param string                    $groupPath         A property path pointing to the property used
     *                                                     to group the choices. Only allowed if
     *                                                     the choices are given as flat array.
     * @param PropertyAccessorInterface $propertyAccessor  The reflection graph for reading property paths.
     */
    public function __construct(ObjectManager $manager, $class, $labelPath = null, $entities = null, $groupPath = null, PropertyAccessorInterface $propertyAccessor = null)
    {
        $this->em = $manager;
        $this->classMetadata = $manager->getClassMetadata($class);
        $this->class = $this->classMetadata->getName();

        $identifier = $this->classMetadata->getIdentifierFieldNames();

        if (1 === count($identifier)) {
            $this->idField = $identifier[0];
            $this->idAsValue = true;

            if (in_array($this->classMetadata->getTypeOfField($this->idField), array('integer', 'smallint', 'bigint'))) {
                $this->idAsIndex = true;
            }
        }

        $entities = array();
        parent::__construct($entities, $labelPath, array(), null, null, $propertyAccessor);
    }

    /**
     * @param array $entities
     */
    public function addEntities($entities)
    {
        if (empty($entities)) {
            return;
        }
        if (!is_array($entities) && !$entities instanceof \Traversable) {
            $entities = array($entities);
        }

        parent::initialize($entities, array(), array());
    }

    /**
     * Returns the entities corresponding to the given values.
     *
     * @param array $values
     *
     * @return array
     *
     * @see Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface
     */
    public function getChoicesForValues(array $values)
    {
        if ($this->idAsValue) {
            if (empty($values)) {
                return array();
            }

            return $this->em->getRepository($this->class)->findBy(array(
                $this->idField => $values
            ));
        }

        return parent::getChoicesForValues($values);
    }

    /**
     * Returns the values corresponding to the given entities.
     *
     * @param array $entities
     *
     * @return array
     *
     * @see Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceListInterface
     */
    public function getValuesForChoices(array $entities)
    {
        // Optimize performance for single-field identifiers. We already
        // know that the IDs are used as values

        // Attention: This optimization does not check choices for existence
        if ($this->idAsValue) {
            $values = array();

            foreach ($entities as $entity) {
                if ($entity instanceof $this->class) {
                    // Make sure to convert to the right format
                    $values[] = $this->fixValue(current($this->getIdentifierValues($entity)));
                }
            }

            return $values;
        }

        return parent::getValuesForChoices($entities);
    }

    /**
     * Creates a new unique index for this entity.
     *
     * If the entity has a single-field identifier, this identifier is used.
     *
     * Otherwise a new integer is generated.
     *
     * @param mixed $entity The choice to create an index for
     *
     * @return integer|string A unique index containing only ASCII letters,
     *                        digits and underscores.
     */
    protected function createIndex($entity)
    {
        if ($this->idAsIndex) {
            return $this->fixIndex(current($this->getIdentifierValues($entity)));
        }

        return parent::createIndex($entity);
    }

    /**
     * Creates a new unique value for this entity.
     *
     * If the entity has a single-field identifier, this identifier is used.
     *
     * Otherwise a new integer is generated.
     *
     * @param mixed $entity The choice to create a value for
     *
     * @return integer|string A unique value without character limitations.
     */
    protected function createValue($entity)
    {
        if ($this->idAsValue) {
            return (string) current($this->getIdentifierValues($entity));
        }

        return parent::createValue($entity);
    }

    /**
     * {@inheritdoc}
     */
    protected function fixIndex($index)
    {
        $index = parent::fixIndex($index);

        // If the ID is a single-field integer identifier, it is used as
        // index. Replace any leading minus by underscore to make it a valid
        // form name.
        if ($this->idAsIndex && $index < 0) {
            $index = strtr($index, '-', '_');
        }

        return $index;
    }

    /**
     * Returns the values of the identifier fields of an entity.
     *
     * Doctrine must know about this entity, that is, the entity must already
     * be persisted or added to the identity map before. Otherwise an
     * exception is thrown.
     *
     * @param object $entity The entity for which to get the identifier
     *
     * @return array          The identifier values
     *
     * @throws RuntimeException If the entity does not exist in Doctrine's identity map
     */
    private function getIdentifierValues($entity)
    {
        if (!$this->em->contains($entity)) {
            throw new RuntimeException(
                'Entities passed to the choice field must be managed. Maybe ' .
                'persist them in the entity manager?'
            );
        }

        $this->em->initializeObject($entity);

        return $this->classMetadata->getIdentifierValues($entity);
    }
}