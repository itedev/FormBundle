<?php

namespace ITE\FormBundle\Form\ChoiceList;

use ITE\Common\Util\ReflectionUtils;
use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;
use Symfony\Component\Form\Extension\Core\View\ChoiceView;

/**
 * Class DynamicChoiceList
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class DynamicChoiceList extends SimpleChoiceList
{
    /**
     * AjaxChoiceList constructor.
     *
     * @param array $choices
     * @param array $preferredChoices
     */
    public function __construct(array $choices = [], array $preferredChoices = [])
    {
        parent::__construct($choices, $preferredChoices);
    }

    public function addDataChoices($data, bool $asPreferred = false): void
    {
        if (!is_array($data) && !($data instanceof \Traversable)) {
            $data = [$data];
        }
        $data = array_combine($data, $data);

        $this->addChoicesInner($data, [], $asPreferred ? $data : []);
    }

    public function clear()
    {
        parent::initialize([], [], []);
    }

    /**
     * @return array|ChoiceView[]
     */
    public function getViews()
    {
        return array_merge($this->getPreferredViews(), $this->getRemainingViews());
    }

    /**
     * @param int $limit
     * @return array
     */
    public function getSlicedViews($limit)
    {
        $views = $this->getViews();

        return array_slice($views, 0, $limit, true);
    }

    protected function addChoicesInner($choices, array $labels, array $preferredChoices)
    {
        $preferredViews = $this->getPreferredViews();
        $remainingViews = $this->getRemainingViews();

        $newChoices = [];
        foreach ($choices as $value => $label) {
            $index = array_search($value, $this->values);
            if (false !== $index) {
                if (in_array($value, $preferredChoices) && isset($remainingViews[$index])) {
                    $preferredViews[$index] = $remainingViews[$index];
                    unset($remainingViews[$index]);
                }
            } else {
                $newChoices[$value] = $label;
            }
        }

        $this->addChoices(
            $preferredViews,
            $remainingViews,
            $newChoices,
            $labels,
            $preferredChoices
        );

        ReflectionUtils::setValue($this, 'preferredViews', $preferredViews);
        ReflectionUtils::setValue($this, 'remainingViews', $remainingViews);
    }
}
