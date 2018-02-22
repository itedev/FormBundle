<?php

namespace ITE\FormBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * Class FileToAjaxDataTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FileToAjaxDataTransformer implements DataTransformerInterface
{
    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $dataName;

    /**
     * @var bool
     */
    private $multiple;

    /**
     * @param string $fileName
     * @param string $dataName
     * @param bool $multiple
     */
    public function __construct(
        $fileName,
        $dataName,
        $multiple
    ) {
        $this->fileName = $fileName;
        $this->dataName = $dataName;
        $this->multiple = $multiple;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return;
        }

        return [
            $this->fileName => null,
            $this->dataName => $value,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return;
        }

        if (!is_array($value)) {
            throw new TransformationFailedException('Expected an array.');
        }

        $file = isset($value[$this->fileName]) ? $value[$this->fileName] : null;
        $data = isset($value[$this->dataName]) ? $value[$this->dataName] : null;

        if ($this->multiple && is_array($file)) {
            $file = array_filter($file);
        }

        if (null !== $file && [] !== $file) {
            return $file;
        } elseif (null !== $data) {
            return $data;
        } else {
            return;
        }
    }
}
