<?php

namespace ITE\FormBundle\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile as BaseUploadedFile;

/**
 * Class AjaxUploadedFile
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class UploadedFile extends BaseUploadedFile
{
    /**
     * @var bool
     */
    private $test = false;

    /**
     * @var string
     */
    private $newPathName;

    /**
     * @var string
     */
    private $newFileName;

    /**
     * @inheritdoc
     */
    public function __construct($path, $originalName, $mimeType = null, $size = null, $error = null, $test = false)
    {
        $this->test = $test;
        parent::__construct($path, $originalName, $mimeType, $size, $error, $test);
    }

    /**
     * @inheritdoc
     */
    public function isValid()
    {
        $isOk = $this->getError() === UPLOAD_ERR_OK;

        return $this->test ? $isOk : $isOk && is_uploaded_file($this->getPathname());
    }


    /**
     * @inheritdoc
     */
    public function move($directory, $name = null)
    {
        if ($this->isValid()) {
            if ($this->test) {
                $target = File::move($directory, $name);

                $this->newPathName = $target->getPathname();
                $this->newFileName = $target->getFilename();

                return $target;
            }

            $target = $this->getTargetFile($directory, $name);

            if (!@move_uploaded_file($this->getPathname(), $target)) {
                $error = error_get_last();
                throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error['message'])));
            }

            @chmod($target, 0666 & ~umask());

            $this->newPathName = $target->getPathname();
            $this->newFileName = $target->getFilename();

            return $target;
        }

        throw new FileException($this->getErrorMessage());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'tmp_name' => $this->getPathname(),
            'name' => $this->getClientOriginalName(),
            'type' => $this->getClientMimeType(),
            'size' => $this->getClientSize(),
            'error' => $this->getError(),
            'test' => $this->isTest(),
        ];
    }

    /**
     * @return bool
     */
    public function isTest()
    {
        return $this->test;
    }

    /**
     * @param bool $test
     */
    public function setTest($test)
    {
        $this->test = $test;
    }

    /**
     * @return bool
     */
    public function hasNewPathname()
    {
        return null !== $this->newPathName;
    }

    /**
     * @return string
     */
    public function getNewPathname()
    {
        return $this->newPathName;
    }

    /**
     * @return mixed
     */
    public function getNewFilename()
    {
        return $this->newFileName;
    }
}