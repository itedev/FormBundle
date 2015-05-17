<?php

namespace ITE\FormBundle\SF\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use ITE\FormBundle\SF\Form\FormView as ClientFormView;

/**
 * Class FormViewBuilder
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormViewBuilder implements FormViewBuilderInterface
{
    /**
     * @param FormView $view
     * @param FormInterface $form
     * @return ClientFormView
     */
    public function createView(FormView $view, FormInterface $form)
    {
        $clientView = new ClientFormView();
        $clientView->setOptions([
            'id' => $view->vars['id'],
            'name' => $view->vars['name'],
            'full_name' => $view->vars['full_name'],
//                'read_only' => $serverView->vars['read_only'],
//                'required' => $serverView->vars['required'],
//                'compound' => $serverView->vars['compound'],
        ]);

        foreach ($form as $childForm) {
            $name = $childForm->getName();
            $childView = $view[$name];

            $childClientView = $this->createView($childView, $childForm);

            if ($childForm->getConfig()->hasAttribute('prototype') && isset($childView->vars['prototype'])) {
                $prototypeForm = $childForm->getConfig()->getAttribute('prototype');
                $prototypeView = $childView->vars['prototype'];

                $prototypeClientView = $this->createView($prototypeView, $prototypeForm);

                $childClientView->setOption('prototype', $prototypeClientView);
                $childClientView->setOption('prototype_name', $childForm->getConfig()->getOption('prototype_name'));
            }

            $clientView->addChild($name, $childClientView);
        }

        return $clientView;
    }
}