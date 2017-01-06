<?php

namespace ITE\FormBundle\Validation\Constraints;

use ITE\FormBundle\Validation\ClientConstraint;

/**
 * Class CardScheme
 *
 * @Annotation
 * @Target({"PROPERTY"})
 *
 * @see Symfony\Component\Validator\Constraints\CardScheme
 * @see Symfony\Component\Validator\Constraints\CardSchemeValidator
 *
 * @author c1tru55 <mr.c1tru55@gmail.com>
 */
class CardScheme extends ClientConstraint
{
    public $message = 'Unsupported card type or invalid card number.';
    public $schemes;

    protected $schemesRegex = [
        // American Express card numbers start with 34 or 37 and have 15 digits.
        'AMEX' => [
            '/^3[47][0-9]{13}$/',
        ],
        // China UnionPay cards start with 62 and have between 16 and 19 digits.
        // Please note that these cards do not follow Luhn Algorithm as a checksum.
        'CHINA_UNIONPAY' => [
            '/^62[0-9]{14,17}$/',
        ],
        // Diners Club card numbers begin with 300 through 305, 36 or 38. All have 14 digits.
        // There are Diners Club cards that begin with 5 and have 16 digits.
        // These are a joint venture between Diners Club and MasterCard, and should be processed like a MasterCard.
        'DINERS' => [
            '/^3(?:0[0-5]|[68][0-9])[0-9]{11}$/',
        ],
        // Discover card numbers begin with 6011, 622126 through 622925, 644 through 649 or 65.
        // All have 16 digits.
        'DISCOVER' => [
            '/^6011[0-9]{12}$/',
            '/^64[4-9][0-9]{13}$/',
            '/^65[0-9]{14}$/',
            '/^622(12[6-9]|1[3-9][0-9]|[2-8][0-9][0-9]|91[0-9]|92[0-5])[0-9]{10}$/',
        ],
        // InstaPayment cards begin with 637 through 639 and have 16 digits.
        'INSTAPAYMENT' => [
            '/^63[7-9][0-9]{13}$/',
        ],
        // JCB cards beginning with 2131 or 1800 have 15 digits.
        // JCB cards beginning with 35 have 16 digits.
        'JCB' => [
            '/^(?:2131|1800|35[0-9]{3})[0-9]{11}$/',
        ],
        // Laser cards begin with either 6304, 6706, 6709 or 6771 and have between 16 and 19 digits.
        'LASER' => [
            '/^(6304|670[69]|6771)[0-9]{12,15}$/',
        ],
        // Maestro cards begin with either 5018, 5020, 5038, 5893, 6304, 6759, 6761, 6762, 6763 or 0604
        // They have between 12 and 19 digits.
        'MAESTRO' => [
            '/^(5018|5020|5038|6304|6759|6761|676[23]|0604)[0-9]{8,15}$/',
        ],
        // All MasterCard numbers start with the numbers 51 through 55. All have 16 digits.
        'MASTERCARD' => [
            '/^5[1-5][0-9]{14}$/',
        ],
        // All Visa card numbers start with a 4. New cards have 16 digits. Old cards have 13.
        'VISA' => [
            '/^4([0-9]{12}|[0-9]{15})$/',
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function __construct($options = null)
    {
        parent::__construct($options);
        $this->setAttribute('schemes', $this->schemesRegex);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultOption()
    {
        return 'schemes';
    }

    /**
     * {@inheritdoc}
     */
    public function getRequiredOptions()
    {
        return ['schemes'];
    }
}
