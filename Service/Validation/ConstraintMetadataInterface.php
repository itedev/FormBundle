<?php

namespace ITE\FormBundle\Service\Validation;

/**
 * Interface ConstraintMetadataInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface ConstraintMetadataInterface
{
    /**
     * Types
     */
    const TYPE_NOT_BLANK = 'notBlank';
    const TYPE_BLANK = 'blank';
    const TYPE_NOT_NULL = 'notNull';
    const TYPE_NULL = 'null';
    const TYPE_TRUE = 'true';
    const TYPE_FALSE = 'false';
    const TYPE_TYPE = 'type';
    const TYPE_EMAIL = 'email';
    const TYPE_URL = 'url';
    const TYPE_REGEX = 'regex';
    const TYPE_IP = 'ip';
    const TYPE_EQUAL_TO = 'equalTo';
    const TYPE_NOT_EQUAL_TO = 'notEqualTo';
    const TYPE_IDENTICAL_TO = 'identicalTo';
    const TYPE_NOT_IDENTICAL_TO = 'notIdenticalTo';
    const TYPE_LESS_THAN = 'lessThan';
    const TYPE_LESS_THAN_OR_EQUAL = 'lessThanOrEqual';
    const TYPE_GREATER_THAN = 'greaterThan';
    const TYPE_GREATER_THAN_OR_EQUAL = 'greaterThanOrEqual';
    const TYPE_DATE = 'date';
    const TYPE_DATETIME = 'dateTime';
    const TYPE_TIME = 'time';
//        const TYPE_UNIQUE_ENTITY = 'uniqueEntity';
    const TYPE_LOCALE = 'locale';
    const TYPE_COUNTRY = 'country';
    const TYPE_CARD_SCHEME = 'cardScheme';
    const TYPE_CURRENCY = 'currency';
    const TYPE_LUHN = 'luhn';
    const TYPE_IBAN = 'iban';
    const TYPE_ISSN = 'issn';
//        const TYPE_USERPASSWORD = 'userPassword';
    const TYPE_LENGTH_EQUAL_TO = 'lengthEqualTo';
    const TYPE_LENGTH_RANGE = 'lengthRange';
    const TYPE_LENGTH_LESS_THAN_OR_EQUAL = 'lengthLessThanOrEqual';
    const TYPE_LENGTH_GREATER_THAN_OR_EQUAL = 'lengthGreaterThanOrEqual';
    const TYPE_RANGE = 'range';
    const TYPE_RANGE_LESS_THAN_OR_EQUAL = 'rangeLessThanOrEqual';
    const TYPE_RANGE_GREATER_THAN_OR_EQUAL = 'rangeGreaterThanOrEqual';
    const TYPE_CHOICE_MULTIPLE = 'choiceMultiple';
    const TYPE_CHOICE_SINGLE = 'choiceSingle';
//        const TYPE_COLLECTION = 'collection';
    const TYPE_COUNT_EQUAL_TO = 'countEqualTo';
    const TYPE_COUNT_RANGE = 'countRange';
    const TYPE_COUNT_LESS_THAN_OR_EQUAL = 'countLessThanOrEqual';
    const TYPE_COUNT_GREATER_THAN_OR_EQUAL = 'countGreaterThanOrEqual';
//        const TYPE_ISBN = 'isbn';
//        const TYPE_CALLBACK = 'callback';
//        const TYPE_EXPRESSION = 'expression';
//        const TYPE_ALL = 'all';
//        const TYPE_VALID = 'valid';
    const TYPE_REPEATED = 'repeated';

    /**
     * Get type
     *
     * @return string
     */
    public function getType();

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions();

    /**
     * @param $name
     * @param null $default
     * @return null
     */
    public function getOption($name, $default = null);
} 