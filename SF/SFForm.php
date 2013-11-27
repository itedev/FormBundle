<?php

namespace ITE\FormBundle\SF;

/**
 * Class SFForm
 * @package ITE\FormBundle\SF
 */
final class SFForm
{
    /**
     * Components
     */
    const COMPONENT_COLLECTION = 'collection';
    const COMPONENT_DYNAMIC_CHOICE = 'dynamic_choice';
    const COMPONENT_AJAX_FILE_UPLOAD = 'ajax_file_upload';
    const COMPONENT_HIERARCHICAL = 'hierarchical';
    const COMPONENT_EDITABLE = 'editable';

    /**
     * @var array
     */
    public static $components = array(
        self::COMPONENT_COLLECTION,
        self::COMPONENT_DYNAMIC_CHOICE,
        self::COMPONENT_AJAX_FILE_UPLOAD,
        self::COMPONENT_HIERARCHICAL,
        self::COMPONENT_EDITABLE,
    );

    /**
     * Plugins
     */
    const PLUGIN_SELECT2 = 'select2';
    const PLUGIN_TINYMCE = 'tinymce';
    const PLUGIN_BOOTSTRAP_COLORPICKER = 'bootstrap_colorpicker';
    const PLUGIN_BOOTSTRAP_DATETIMEPICKER = 'bootstrap_datetimepicker';
    const PLUGIN_BOOTSTRAP_DATETIMEPICKER2 = 'bootstrap_datetimepicker2';
    const PLUGIN_FILEUPLOAD = 'fileupload';
    const PLUGIN_FINEUPLOADER = 'fineuploader';
    const PLUGIN_MINICOLORS = 'minicolors';
    const PLUGIN_KNOB = 'knob';
    const PLUGIN_STARRATING = 'starrating';
    const PLUGIN_X_EDITABLE = 'x_editable';
//    const PLUGIN_FORM = 'form';

    /**
     * @var array
     */
    public static $plugins = array(
        self::PLUGIN_SELECT2,
        self::PLUGIN_TINYMCE,
        self::PLUGIN_BOOTSTRAP_COLORPICKER,
        self::PLUGIN_BOOTSTRAP_DATETIMEPICKER,
        self::PLUGIN_BOOTSTRAP_DATETIMEPICKER2,
        self::PLUGIN_FILEUPLOAD,
        self::PLUGIN_FINEUPLOADER,
        self::PLUGIN_MINICOLORS,
        self::PLUGIN_KNOB,
        self::PLUGIN_STARRATING,
        self::PLUGIN_X_EDITABLE,
//        self::PLUGIN_FORM,
    );

}