<?php

/**
 * Class Manager_Core_PolicyNumber
 *
 * @author April Portus <april.portus@barbon.com>
 */
class Manager_Core_PolicyNumber
{
    //Model Manager Core_ApplicationUtilities

    /**
     * Prefix used to identify policies
     */
    const POLICY_IDENTIFIER = 'P';

    /**
     * Prefix used to identify quotes
     */
    const QUOTE_IDENTIFIER = 'Q';

    /**
     * Prefix used to identify RRP policies moved from Insight
     */
    const INSIGHT_RRP_POLICY_IDENTIFIER = 'RRPI';

    /**
     * White Label Identifier for HomeLet
     */
    const WHITE_LABEL_HOMELET = 'HL';

    /**
     * Insurance Identifier
     */
    const INSURANCE_IDENTIFIER = 'I';

    /**
     * @var string
     */
    private $whiteLabelId;

    /**
     * @var string
     */
    private $typeIdentifier;

    /**
     * Constructor
     *
     * @param string $whiteLabelId
     * @param string $typeIdentifier
     * @throws \Exception
     */
    public function __construct($whiteLabelId=self::WHITE_LABEL_HOMELET, $typeIdentifier=self::INSURANCE_IDENTIFIER)
    {
        switch ($whiteLabelId) {
            case self::WHITE_LABEL_HOMELET:
                break;
            default:
                throw new \Exception('Invalid white label');
        }
        $this->whiteLabelId = $whiteLabelId;

        switch ($typeIdentifier) {
            case self::INSURANCE_IDENTIFIER:
                break;
            default:
                throw new \Exception('Invalid type identifier');
        }
        $this->typeIdentifier = $typeIdentifier;
    }

    /**
     * Returns true if it's a policy number
     *
     * @param string $policyNumber
     * @return bool
     */
    public static function isPolicy($policyNumber)
    {
        if ($policyNumber[0] == self::POLICY_IDENTIFIER) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if it's a RRPI policy number
     *
     * @param $policyNumber
     * @return bool
     */
    public static function isRentRecoveryPlusPolicy($policyNumber)
    {
        if (substr($policyNumber, 0, 4) ==
            sprintf(
                '%s%s%s',
                self::POLICY_IDENTIFIER,
                self::WHITE_LABEL_HOMELET,
                self::INSURANCE_IDENTIFIER
            )) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if it's a quote
     *
     * @param $policyNumber
     * @return bool
     */
    public static function isQuote($policyNumber)
    {
        if ($policyNumber[0] == self::QUOTE_IDENTIFIER) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if it's a RRPI quote number
     *
     * @param $policyNumber
     * @return bool
     */
    public static function isRentRecoveryPlusQuote($policyNumber)
    {
        if (substr($policyNumber, 0, 4) ==
            sprintf(
                '%s%s%s',
                self::QUOTE_IDENTIFIER,
                self::WHITE_LABEL_HOMELET,
                self::INSURANCE_IDENTIFIER
            )) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if it's a RRPI policy number from Insight
     *
     * @param $policyNumber
     * @return bool
     */
    public static function isRentRecoveryPlusInsightPolicy($policyNumber)
    {
        if (substr($policyNumber, 0, 4) == self::INSIGHT_RRP_POLICY_IDENTIFIER) {
            return true;
        }
        return false;
    }

    /**
     * Generates the next policy/quote number
     *
     * @param $identifier
     * @param int $policyTerm
     * @return string
     * @throws \Exception
     */
    public function generateApplicationNumber($identifier, $policyTerm=1)
    {
        switch ($identifier) {
            case self::QUOTE_IDENTIFIER:
                continue;
            case self::POLICY_IDENTIFIER:
                break;
            default:
                throw new \Exception('Invalid application type');
        }

        $numberTracker = new \Datasource_Core_NumberTracker();
        $policyNumber = sprintf(
            '%s%s%s%07d/%02d',
            $identifier,
            $this->whiteLabelId,
            $this->typeIdentifier,
            $numberTracker->getNextNumberByPolicyName($numberTracker::NEXT_POLICY_NUMBER_IDENTIFIER),
            $policyTerm
        );

        return $policyNumber;
    }

    /**
     * Converts the given quote number to a policy number
     *
     * @param $applicationNumber
     * @return string
     */
    public static function convertQuoteToPolicyNumber($applicationNumber)
    {
        return sprintf('%s%s', self::POLICY_IDENTIFIER, substr($applicationNumber, 1));
    }
}