<?php

namespace ITE\FormBundle\Form\DataTransformer;

use ITE\FormBundle\File\UploadedFile;
use Symfony\Component\Form\DataTransformerInterface;

/**
 * Class FileToStringTransformer
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class FileToStringTransformer implements DataTransformerInterface
{
    /**
     * @var bool
     */
    private $multiple;

    /**
     * @var string
     */
    private $uploadDir;

    /**
     * @param bool $multiple
     * @param string $uploadDir
     */
    public function __construct($multiple, $uploadDir)
    {
        $this->multiple = $multiple;
        $this->uploadDir = $uploadDir;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($value)
    {
        if (null === $value) {
            return;
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        $data = [];
        /** @var UploadedFile $file */
        foreach ($value as $file) {
            $data[] = [
                'fileName' => $file->getFilename(),
                'originalName' => $file->getClientOriginalName(),
                'type' => $file->getClientMimeType(),
                'size' => $file->getClientSize(),
            ];
        }

        return json_encode($data);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($value)
    {
        if (null === $value) {
            return;
        }

        $fileDataItems = json_decode($value, true);
        $files = [];
        foreach ($fileDataItems as $fileDataItem) {
            $file = new UploadedFile(
                $this->uploadDir . DIRECTORY_SEPARATOR . $fileDataItem['fileName'],
                $fileDataItem['originalName'],
                isset($fileDataItem['type']) ? $fileDataItem['type'] : null,
                isset($fileDataItem['size']) ? $fileDataItem['size'] : null,
                null,
                true
            );
            $files[] = $file;
        }

        if ($this->multiple) {
            return $files;
        }

        return false !== reset($files) ? reset($files) : null;
    }
}
