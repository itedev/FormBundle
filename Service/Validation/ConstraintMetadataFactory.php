<?php

namespace ITE\FormBundle\Service\Validation;

use Doctrine\Common\Inflector\Inflector;
use ITE\FormBundle\Util\FormUtils;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints\AbstractComparison;
use Symfony\Component\Validator\Constraints\CardScheme;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Ip;
use Symfony\Component\Validator\Constraints\Issn;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Type;
use Symfony\Component\Validator\Constraints\Url;

/**
 * Class ConstraintMetadataFactory
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class ConstraintMetadataFactory
{
    /**
     * @var TranslatorInterface $translator
     */
    protected $translator;

    /**
     * @var string|null $translationDomain
     */
    protected $translationDomain;

    /**
     * @var array $constraintMetadataCache
     */
    protected $constraintMetadataCache = array();

    /**
     * @param TranslatorInterface $translator
     * @param null $translationDomain
     */
    public function __construct(TranslatorInterface $translator, $translationDomain = null)
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @param Constraint $constraint
     * @return null
     */
    public function getMetadataForConstraint(Constraint $constraint)
    {
        $hash = md5(serialize($constraint));
        if (isset($this->constraintMetadataCache[$hash])) {
            return $this->constraintMetadataCache[$hash];
        }

        if (null === $constraintConverter = $this->getConstraintConverter($constraint)) {
            return null;
        }

        $this->constraintMetadataCache[$hash] = new ConstraintMetadata(
            $constraintConverter->getType(),
            $constraintConverter->getMessage(),
            $constraintConverter->getOptions()
        );

        return $this->constraintMetadataCache[$hash];
    }

    /**
     * @param FormInterface $form
     * @return null
     */
    public function getMetadataForForm(FormInterface $form)
    {
        $config = $form->getConfig();
        if (!FormUtils::isResolvedFormTypeChildOf($config->getType(), 'repeated')) {
            return null;
        }

        $type = ConstraintMetadataInterface::TYPE_REPEATED;
        $message = $config->getOption('invalid_message');
        $hash = md5(serialize(array(
            'type' => $type,
            'message' => $message,
        )));
        if (isset($this->constraintMetadataCache[$hash])) {
            return $this->constraintMetadataCache[$hash];
        }

        $this->constraintMetadataCache[$hash] = new ConstraintMetadata(
            $type,
            $this->translator->trans($message, array(), $this->translationDomain),
            array()
        );

        return $this->constraintMetadataCache[$hash];
    }

    /**
     * @param Constraint $constraint
     * @return ConstraintConverterInterface|null
     */
    protected function getConstraintConverter(Constraint $constraint)
    {
        $constraintClass = get_class($constraint);
        if (0 !== strpos($constraintClass, 'Symfony\Component\Validator\Constraints')) {
            return null;
        }
        $constraintClassName = substr($constraintClass, strrpos($constraintClass, '\\') + 1);
        $constraintConverterClass = sprintf('ITE\FormBundle\Service\Validation\Constraints\%sConverter', $constraintClassName);
        if (!class_exists($constraintConverterClass)) {
            return null;
        }

        return new $constraintConverterClass($constraint, $this->translator, $this->translationDomain);
    }

} 