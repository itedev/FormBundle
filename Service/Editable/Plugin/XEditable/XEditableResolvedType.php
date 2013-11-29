<?php

namespace ITE\FormBundle\Service\Editable\Plugin\XEditable;

/**
 * Class XEditableResolvedType
 * @package ITE\FormBundle\Service\Editable\Plugin\XEditable
 */
class XEditableResolvedType
{
    /**
     * @var string $xType
     */
    protected $xEditableType;

    /**
     * @var string $sfType
     */
    protected $sfType;

    /**
     * @var string $sfBaseType
     */
    protected $sfBaseType;

    /**
     * @param $xEditableType
     * @param $sfType
     * @param $sfBaseType
     */
    public function __construct($xEditableType, $sfType, $sfBaseType)
    {
        $this->xEditableType = $xEditableType;
        $this->sfType = $sfType;
        $this->sfBaseType = $sfBaseType;
    }

    /**
     * Get sfBaseType
     *
     * @return string
     */
    public function getSfBaseType()
    {
        return $this->sfBaseType;
    }

    /**
     * Get sfType
     *
     * @return string
     */
    public function getSfType()
    {
        return $this->sfType;
    }

    /**
     * Get xEditableType
     *
     * @return string
     */
    public function getXEditableType()
    {
        return $this->xEditableType;
    }

    /**
     * @param $xEditableType
     * @param $sfType
     * @param $sfBaseType
     * @return XEditableResolvedType
     */
    public static function create($xEditableType, $sfType, $sfBaseType)
    {
        return new self($xEditableType, $sfType, $sfBaseType);
    }
} 