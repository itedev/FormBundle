<?php

namespace ITE\FormBundle;

/**
 * Class Components
 * @package ITE\FormBundle
 */
final class Components
{
    const COLLECTION = 'collection';
    const DYNAMIC_CHOICE = 'dynamic_choice';
    const AJAX_FILE_UPLOAD = 'ajax_file_upload';
    const HIERARCHICAL = 'hierarchical';

    public static $components = array(
        self::COLLECTION,
        self::DYNAMIC_CHOICE,
        self::AJAX_FILE_UPLOAD,
        self::HIERARCHICAL,
    );

}