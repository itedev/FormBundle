<?php

namespace ITE\FormBundle\Validation;

/**
 * Interface NormConstraintInterface
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
interface NormConstraintInterface
{
    // Symfony\Component\Validator\Constraints
    const TYPE_ALL = 'all'; // [?]
    const TYPE_BLANK = 'blank'; // [+]
    const TYPE_CALLBACK = 'callback'; // [?]
    const TYPE_CARD_SCHEME = 'card_scheme';
    const TYPE_CHOICE = 'choice';
    const TYPE_COLLECTION = 'collection'; // [?]
    const TYPE_COUNT = 'count';
    const TYPE_COUNTRY = 'country';
    const TYPE_CURRENCY = 'currency';
    const TYPE_DATE = 'date';
    const TYPE_DATE_TIME = 'date_time';
    const TYPE_EMAIL = 'email';
    const TYPE_EQUAL_TO = 'equal_to'; // [+]
    const TYPE_EXPRESSION = 'expression'; // [?]
    const TYPE_FALSE = 'false'; // [+]
    const TYPE_FILE = 'file';
    const TYPE_GREATER_THAN = 'greater_than'; // [+]
    const TYPE_GREATER_THAN_OR_EQUAL = 'greater_than_or_equal'; // [+]
    const TYPE_IBAN = 'iban';
    const TYPE_IDENTICAL_TO = 'identical_to'; // [+]
    const TYPE_IMAGE = 'image';
    const TYPE_IP = 'ip';
    const TYPE_ISBN = 'isbn';
    const TYPE_ISSN = 'issn';
    const TYPE_LANGUAGE = 'language';
    const TYPE_LENGTH = 'length';
    const TYPE_LESS_THAN = 'less_than'; // [+]
    const TYPE_LESS_THAN_OR_EQUAL = 'less_than_or_equal'; // [+]
    const TYPE_LOCALE = 'locale';
    const TYPE_LUHN = 'luhn';
    const TYPE_NOT_BLANK = 'not_blank'; // [+]
    const TYPE_NOT_EQUAL_TO = 'not_equal_to'; // [+]
    const TYPE_NOT_IDENTICAL_TO = 'not_identical_to'; // [+]
    const TYPE_NOT_NULL = 'not_null'; // [+]
    const TYPE_NULL = 'null'; // [+]
//    const TYPE_OPTIONAL = 'optional'; // internal
    const TYPE_RANGE = 'range';
    const TYPE_REGEX = 'regex';
//    const TYPE_REQUIRED = 'required'; // internal
    const TYPE_TIME = 'time';
    const TYPE_TRAVERSE = 'traverse';
    const TYPE_TRUE = 'true'; // [+]
    const TYPE_TYPE = 'type';
    const TYPE_URL = 'url';
    const TYPE_UUID = 'uuid';
    const TYPE_VALID = 'valid'; // [?]
    // Symfony\Component\Security\Core\Validator\Constraints
    const TYPE_USER_PASSWORD = 'user_password';
    // Symfony\Bridge\Doctrine\Validator\Constraints
    const TYPE_UNIQUE_ENTITY = 'unique_entity';
    // derived constraints
    const TYPE_LENGTH_EQUAL_TO = 'length_equal_to';
    const TYPE_LENGTH_RANGE = 'length_range';
    const TYPE_LENGTH_LESS_THAN_OR_EQUAL = 'length_less_than_or_equal';
    const TYPE_LENGTH_GREATER_THAN_OR_EQUAL = 'length_greater_than_or_equal';
    const TYPE_RANGE_LESS_THAN_OR_EQUAL = 'range_less_than_or_equal';
    const TYPE_RANGE_GREATER_THAN_OR_EQUAL = 'range_greater_than_or_equal';
    const TYPE_CHOICE_MULTIPLE = 'choice_multiple';
    const TYPE_CHOICE_SINGLE = 'choice_single'; // [+]
    const TYPE_COUNT_EQUAL_TO = 'count_equal_to'; // [+]
    const TYPE_COUNT_RANGE = 'count_range';
    const TYPE_COUNT_LESS_THAN_OR_EQUAL = 'count_less_than_or_equal';
    const TYPE_COUNT_GREATER_THAN_OR_EQUAL = 'count_greater_than_or_equal';
    // extra
    const TYPE_REPEATED = 'repeated';
}
