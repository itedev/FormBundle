<?php

namespace ITE\FormBundle\Form\ChoiceList;

use ITE\Common\Util\ReflectionUtils;
use Symfony\Component\Form\Exception\InvalidConfigurationException;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\FormConfigBuilder;

/**
 * Class AjaxChoiceList
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxChoiceList extends SimpleChoiceList
{
    public function __construct(array $choices = [], array $preferredChoices = [])
    {
        parent::__construct($choices, $preferredChoices);
    }

    public function addDataChoices($data, array $labels = [], bool $asPreferred = false): void
    {
        if (!is_array($data) && !($data instanceof \Traversable)) {
            $data = [$data];
        }

        $labels = !empty($data) && empty($labels) ? $data : $labels;
        $choices = array_combine($data, $labels);

        $this->addChoicesInner($choices, $asPreferred ? $choices : []);
    }

    protected function addChoicesInner($choices, array $preferredChoices): void
    {
        $preferredViews = $this->getPreferredViews();
        $remainingViews = $this->getRemainingViews();

        $newChoices = [];
        foreach ($choices as $choice => $label) {
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
                $newChoices[$choice] = $label;
            }
        }
        $choices = $newChoices;

        if (empty($choices)) {
            return;
        }

        $this->addChoices(
            $preferredViews,
            $remainingViews,
            $choices,
            $choices,
            $preferredChoices
        );

        ReflectionUtils::setValue($this, 'preferredViews', $preferredViews);
        ReflectionUtils::setValue($this, 'remainingViews', $remainingViews);
    }
}
