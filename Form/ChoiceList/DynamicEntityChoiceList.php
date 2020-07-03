<?php

namespace ITE\FormBundle\Form\ChoiceList;

use Doctrine\Common\Persistence\ObjectManager;
use ITE\Common\Util\ReflectionUtils;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityLoaderInterface;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Exception\StringCastException;
use Symfony\Component\Form\FormConfigBuilder;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;

/**
 * Class DynamicEntityChoiceList
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DynamicEntityChoiceList extends EntityChoiceList
{
    /**
     * @var string
     */
    private $labelPath;

    /**
     * @var string
     */
    private $groupPath;

    /**
     * @var PropertyAccessorInterface
     */
    private $propertyAccessor;

    public function __construct(
        ObjectManager $manager,
        $class,
        $labelPath = null,
        EntityLoaderInterface $entityLoader = null,
        $entities = null,
        array $preferredEntities = array(),
        $groupPath = null,
        PropertyAccessorInterface $propertyAccessor = null
    ) {
        parent::__construct(
            $manager,
            $class,
            $labelPath,
            $entityLoader,
            $entities,
            $preferredEntities,
            $groupPath,
            $propertyAccessor
        );

        $this->labelPath = $labelPath;
        $this->groupPath = $groupPath;
        $this->propertyAccessor = $propertyAccessor ?: PropertyAccess::createPropertyAccessor();
    }

    public function addDataChoices($data, bool $asPreferred = false): void
    {
        if (!is_array($data) && !($data instanceof \Traversable)) {
            $data = [$data];
        }

        $this->addChoicesInner($data, [], $asPreferred ? $data : []);
    }

//    public function clear()
//    {
//        parent::initialize([], [], []);
//    }
//
//    /**
//     * @return array|ChoiceView[]
//     */
//    public function getViews()
//    {
//        return array_merge($this->getPreferredViews(), $this->getRemainingViews());
//    }
//
//    /**
//     * @param int $limit
//     * @return array
//     */
//    public function getSlicedViews($limit)
//    {
//        $views = $this->getViews();
//
//        return array_slice($views, 0, $limit, true);
//    }

    protected function addChoicesInner($choices, array $labels, array $preferredChoices): void
    {
        $preferredViews = $this->getPreferredViews();
        $remainingViews = $this->getRemainingViews();

        $newChoices = [];
        foreach ($choices as $i => $choice) {
            $index = $this->createIndex($choice);

            if ('' === $index || null === $index || !FormConfigBuilder::isValidName((string) $index)) {
                throw new InvalidConfigurationException(sprintf('The index "%s" created by the choice list is invalid. It should be a valid, non-empty Form name.', $index));
            }

            if (isset($this->choices[$index])) {
                if (in_array($choice, $preferredChoices) && isset($remainingViews[$index])) {
                    $preferredViews[$index] = $remainingViews[$index];
                    unset($remainingViews[$index]);
                }
            } else {
                $newChoices[] = $choice;
            }
        }
        $choices = $newChoices;

        if (empty($choices)) {
            return;
        }

        if (null !== $this->groupPath) {
            $groupedChoices = [];

            foreach ($choices as $i => $choice) {
                if (is_array($choice)) {
                    throw new InvalidArgumentException('You should pass a plain object array (without groups) when using the "groupPath" option.');
                }

                try {
                    $group = $this->propertyAccessor->getValue($choice, $this->groupPath);
                } catch (NoSuchPropertyException $e) {
                    // Don't group items whose group property does not exist
                    // see https://github.com/symfony/symfony/commit/d9b7abb7c7a0f28e0ce970afc5e305dce5dccddf
                    $group = null;
                }

                if (null === $group) {
                    $groupedChoices[$i] = $choice;
                } else {
                    $groupName = (string) $group;

                    if (!isset($groupedChoices[$groupName])) {
                        $groupedChoices[$groupName] = [];
                    }

                    $groupedChoices[$groupName][$i] = $choice;
                }
            }

            $choices = $groupedChoices;
        }

        $labels = [];
        $this->extractLabels($choices, $labels);

        $this->addChoices(
            $preferredViews,
            $remainingViews,
            $choices,
            $labels,
            $preferredChoices
        );

        ReflectionUtils::setValue($this, 'preferredViews', $preferredViews);
        ReflectionUtils::setValue($this, 'remainingViews', $remainingViews);
    }

    protected function extractLabels($choices, array &$labels): void
    {
        foreach ($choices as $i => $choice) {
            if (is_array($choice)) {
                $labels[$i] = [];
                $this->extractLabels($choice, $labels[$i]);
            } elseif ($this->labelPath) {
                $labels[$i] = $this->propertyAccessor->getValue($choice, $this->labelPath);
            } elseif (method_exists($choice, '__toString')) {
                $labels[$i] = (string) $choice;
            } else {
                throw new StringCastException(sprintf('A "__toString()" method was not found on the objects of type "%s" passed to the choice field. To read a custom getter instead, set the argument $labelPath to the desired property path.', get_class($choice)));
            }
        }
    }
}
