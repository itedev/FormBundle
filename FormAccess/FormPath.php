<?php

namespace ITE\FormBundle\FormAccess;

/**
 * Class FormPath
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FormPath implements FormPathInterface
{
    /**
     * @var array
     */
    private $elements = [];

    /**
     * @var array
     */
    private $isParent = [];

    /**
     * @var int
     */
    private $length;

    /**
     * @var bool
     */
    private $absolute;

    /**
     * @var string
     */
    private $pathAsString;

    /**
     * @param $formPath
     */
    public function __construct($formPath)
    {
        if ($formPath instanceof FormPath) {
            /* @var FormPath $formPath */
            $this->elements = $formPath->elements;
            $this->length = $formPath->length;
            $this->relative = $formPath->relative;
            $this->pathAsString = $formPath->pathAsString;

            return;
        }

        if (!is_string($formPath)) {
            throw new \InvalidArgumentException($formPath, 'string or ITE\FormBundle\FormAccess\FormPath');
        }
        if ('' === $formPath) {
            throw new \InvalidArgumentException('The form path should not be empty.');
        }
        if ('/' === substr($formPath, -1)) {
            throw new \InvalidArgumentException('The form path must not end with "/" symbol.');
        }

        $this->pathAsString = $formPath;

        $this->absolute = false;
        if ('/' === substr($formPath, 0, 1)) {
            $formPath = substr($formPath, 1);
            $this->absolute = true;
        }

        $elements = explode('/', $formPath);
        foreach ($elements as $i => $element) {
            $this->elements[] = $element;
            $this->isParent[] = '..' === $element;
        }

        $this->length = count($this->elements);
    }

    /**
     * {@inheritdoc}
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * {@inheritdoc}
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * {@inheritdoc}
     */
    public function isAbsolute()
    {
        return $this->absolute;
    }

    /**
     * {@inheritdoc}
     */
    public function isRelative()
    {
        return !$this->absolute;
    }

    /**
     * {@inheritdoc}
     */
    public function getElement($index)
    {
        if (!isset($this->elements[$index])) {
            throw new \OutOfBoundsException(sprintf('The index %s is not within the form path', $index));
        }

        return $this->elements[$index];
    }

    /**
     * {@inheritdoc}
     */
    public function isParent($index)
    {
        if (!isset($this->isParent[$index])) {
            throw new \OutOfBoundsException(sprintf('The index %s is not within the form path', $index));
        }

        return $this->isParent[$index];
    }

    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return $this->pathAsString;
    }
}