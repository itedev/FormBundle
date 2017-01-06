<?php

namespace ITE\FormBundle\Form\ChoiceList;

use Symfony\Component\Form\Extension\Core\ChoiceList\SimpleChoiceList;

/**
 * Class AjaxChoiceList
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class AjaxChoiceList extends SimpleChoiceList
{
    public function __construct()
    {
        parent::__construct([], []);
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        if (!is_array($data) && !($data instanceof \Traversable)) {
            $data = [$data];
        }
        parent::initialize($data, [], []);
    }
}
