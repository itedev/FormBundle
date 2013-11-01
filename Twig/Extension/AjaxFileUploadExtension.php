<?php

namespace ITE\FormBundle\Twig\Extension;

use Twig_Environment;
use Twig_Extension;
use Twig_Template;

/**
 * Class AjaxFileUploadExtension
 * @package ITE\FormBundle\Twig\Extension
 */
class AjaxFileUploadExtension extends Twig_Extension
{
    /**
     * @return array
     */
    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('ite_debug', array($this, 'debug'), array('needs_context' => true, 'needs_environment' => true)),
        );
    }

    /**
     * @param Twig_Environment $env
     * @param $context
     */
    public function debug(Twig_Environment $env, $context)
    {
        if (!$env->isDebug()) {
            return;
        }

        $variables = array();
        $count = func_num_args();
        if (2 === $count) {
            foreach ($context as $key => $value) {
                if (!$value instanceof Twig_Template) {
                    $variables[$key] = $value;
                }
            }
        } else {
            for ($i = 2; $i < $count; $i++) {
                $variables[] = func_get_arg($i);
            }
        }

        call_user_func(function() use ($variables) {
            if (function_exists('xdebug_break')) {
                xdebug_break();
            }
        });
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'ite_form.twig.ajax_file_upload_extension';
    }

}