<?php

namespace ITE\FormBundle\SF\Form;

use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView as ServerFormView;

/**
 * Class FormViewBuilder
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormViewBuilder
{
    /**
     * @param ServerFormView $serverView
     * @param FormInterface $form
     * @return FormView
     */
    public function createView(ServerFormView $serverView, FormInterface $form)
    {
        $view = new FormView();
        $view->setOptions([
            'id' => $serverView->vars['id'],
            'name' => $serverView->vars['name'],
            'full_name' => $serverView->vars['full_name'],
//                'read_only' => $serverView->vars['read_only'],
//                'required' => $serverView->vars['required'],
//                'compound' => $serverView->vars['compound'],
        ]);

        foreach ($form as $childForm) {
            $name = $childForm->getName();
            $childServerView = $serverView[$name];

            $childView = $this->createView($childServerView, $childForm);

            if ($childForm->getConfig()->hasAttribute('prototype') && isset($childServerView->vars['prototype'])) {
                $prototypeForm = $childForm->getConfig()->getAttribute('prototype');
                $prototypeServerView = $childServerView->vars['prototype'];

                $prototypeFormView = $this->createView($prototypeServerView, $prototypeForm);

                $childView->setOption('prototype', $prototypeFormView);
                $childView->setOption('prototype_name', $childForm->getConfig()->getOption('prototype_name'));
            }

            $view->addChild($name, $childView);
        }

        return $view;
    }
}