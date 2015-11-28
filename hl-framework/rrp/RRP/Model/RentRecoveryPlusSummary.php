<?php

namespace RRP\Model;
use RRP\Utility\PolicyOptionsManager;

/**
 * Class RentRecoveryPlusSummary
 *
 * @package RRP\Model
 * @author April Portus <april.portus@barbon.com>
 */
class RentRecoveryPlusSummary extends AbstractResponseModel
{
    /**
     * Policy option for RRP
     */
    const POLICY_OPTION_RRP = 'rentguaranteerrp';

    /**
     * Policy option for RRP wiht nill excess
     */
    const POLICY_OPTION_RRP_NIL_EXCESS = 'rentguaranteenilexcessrrp';

    /**
     * @var string
     */
    private $policyNumber;

    /**
     * @var int
     */
    private $policyLength;

    /**
     * @var string
     */
    private $amountsCovered;

    /**
     * @var string
     */
    private $propertyAddress1;

    /**
     * @var string
     */
    private $propertyAddress2;

    /**
     * @var string
     */
    private $propertyAddress3;

    /**
     * @var string
     */
    private $propertyPostcode;

    /**
     * @var string
     */
    private $policyOptions;

    /**
     * @var string
     */
    private $policyStartAt;

    /**
     * @var string
     */
    private $landlordTitle;

    /**
     * @var string
     */
    private $landlordFirstName;

    /**
     * @var string
     */
    private $landlordLastName;

    /**
     * @var string
     */
    private $landlordEmailAddress;

    /**
     * @var string
     */
    private $landlordPhoneNumber;

    /**
     * @var string
     */
    private $cancelledDate;

    /**
     * @var string
     */
    private $payStatus;

    /**
     * Hydrate a single record
     *
     * @param array $data
     * @return object
     */
    public static function hydrate($data)
    {
        return self::hydrateModelProperties(
            new self(),
            $data,
            self::getMappedProperties(),
            array()
        );
    }

    /**
     * Hydrate from the database row names
     *
     * @param $data
     * @return object
     */
    public static function hydrateFromRow($data)
    {
        return self::hydrateModelProperties(
            new self(),
            $data,
            self::getDBNameProperties(),
            array()
        );
    }

    /**
     * As above but these fields have different names in the two classes
     *
     * @return array
     */
    private static function getMappedProperties()
    {
        return array(
            'startDate' => 'PolicyStartAt',
        );
    }

    /**
     * Gets a array of the mapping between the database table name and class properties
     *
     * @return array
     */
    private static function getDBNameProperties()
    {
        return array(
            'policynumber'    => 'policyNumber',
            'startdate'       => 'policyStartAt',
            'cancdate'        => 'cancelledDate',
            'propaddress1'    => 'propertyAddress1',
            'propaddress3'    => 'propertyAddress2',
            'propaddress5'    => 'propertyAddress3',
            'proppostcode'    => 'propertyPostcode',
            'policylength'    => 'policyLength',
            'title'           => 'landlordTitle',
            'firstname'       => 'landlordFirstName',
            'lastname'        => 'landlordLastName',
            'email'           => 'landlordEmailAddress',
            'phone'           => 'landlordPhoneNumber',
            'amountscovered'  => 'amountsCovered',
            'policyoptions'   => 'policyOptions',
            'paystatus'       => 'payStatus'
        );
    }

    /**
     * Converts the date to database format
     *
     * @param mixed $inputDate
     * @return string
     */
    private function transformDate($inputDate)
    {
        if (
            null === $inputDate ||
            '00-00-0000' == $inputDate ||
            '0000-00-00' == $inputDate ||
            '' == $inputDate
        ) {
            return '0000-00-00';
        }
        else if ($inputDate instanceof \DateTime) {
            $returnDate = $inputDate->format('Y-m-d');
        }
        else {
            $returnDate = date('Y-m-d', strtotime(str_replace('/', '-', $inputDate)));
        }
        return $returnDate;
    }

    /**
     * Gets the policy number
     *
     * @return string
     */
    public function getPolicyNumber()
    {
        return $this->policyNumber;
    }

    /**
     * Sets the policy number
     *
     * @param string $policyNumber
     * @return $this
     */
    public function setPolicyNumber($policyNumber)
    {
        $this->policyNumber = $policyNumber;
        return $this;
    }

    /**
     * Gets the excess string
     *
     * @return string
     */
    public function getExcess()
    {
        if ($this->getIsNilExcess()) {
            return 'Nil Excess';
        }
        return '1 Month Excess';
    }

    /**
     * Gets the 'isNilExcess' flag
     *
     * @return boolean
     */
    public function getIsNilExcess()
    {
        return PolicyOptionsManager::isOptionSet(
            $this->policyOptions,
            self::POLICY_OPTION_RRP_NIL_EXCESS,
            $this->amountsCovered
        );
    }

    /**
     * Gets the amounts covered
     *
     * @return string
     */
    public function getAmountsCovered()
    {
        return $this->amountsCovered;
    }

    /**
     * Sets the amounts covered
     *
     * @param string $amountsCovered
     * @return $this
     */
    public function setAmountsCovered($amountsCovered)
    {
        $this->amountsCovered = $amountsCovered;
        return $this;
    }

    /**
     * Gets the policy length
     *
     * @return int
     */
    public function getPolicyLength()
    {
        return $this->policyLength;
    }

    /**
     * Sets the policy length
     *
     * @param int $policyLength
     * @return $this
     */
    public function setPolicyLength($policyLength)
    {
        $this->policyLength = $policyLength;
        return $this;
    }

    /**
     * Gets the landlord email address
     *
     * @return string
     */
    public function getLandlordEmailAddress()
    {
        return $this->landlordEmailAddress;
    }

    /**
     * Sets the landlord email address
     *
     * @param string $landlordEmailAddress
     * @return $this
     */
    public function setLandlordEmailAddress($landlordEmailAddress)
    {
        $this->landlordEmailAddress = $landlordEmailAddress;
        return $this;
    }

    /**
     * Gets the landlord full name including title
     *
     * @return string
     */
    public function getLandlordFullName()
    {
        return sprintf('%s %s %s', $this->landlordTitle, $this->landlordFirstName, $this->landlordLastName);
    }

    /**
     * Gets the landlord first name
     *
     * @return string
     */
    public function getLandlordFirstName()
    {
        return $this->landlordFirstName;
    }

    /**
     * Sets the landlord first name
     *
     * @param string $landlordFirstName
     * @return $this
     */
    public function setLandlordFirstName($landlordFirstName)
    {
        $this->landlordFirstName = $landlordFirstName;
        return $this;
    }

    /**
     * Gets the landlord last name
     *
     * @return string
     */
    public function getLandlordLastName()
    {
        return $this->landlordLastName;
    }

    /**
     * Sets the landlord last name
     *
     * @param string $landlordLastName
     * @return $this
     */
    public function setLandlordLastName($landlordLastName)
    {
        $this->landlordLastName = $landlordLastName;
        return $this;
    }

    /**
     * Gets the landlord phone number
     *
     * @return string
     */
    public function getLandlordPhoneNumber()
    {
        return $this->landlordPhoneNumber;
    }

    /**
     * Sets the landlord phone number
     *
     * @param string $landlordPhoneNumber
     * @return $this
     */
    public function setLandlordPhoneNumber($landlordPhoneNumber)
    {
        $this->landlordPhoneNumber = $landlordPhoneNumber;
        return $this;
    }

    /**
     * Sets the landlord title
     *
     * @return string
     */
    public function getLandlordTitle()
    {
        return $this->landlordTitle;
    }

    /**
     * Gets the landlord title
     *
     * @param string $landlordTitle
     * @return $this
     */
    public function setLandlordTitle($landlordTitle)
    {
        $this->landlordTitle = $landlordTitle;
        return $this;
    }

    /**
     * Gets the policy end date string
     *
     * @return string
     */
    public function getPolicyEndAt()
    {
        $policyEndAt = date('d/m/Y', strtotime($this->policyStartAt . ' + ' . $this->policyLength . ' Month - 1 Day'));
        return $this->transformDate($policyEndAt);
    }

    /**
     * Gets the policy start date string
     *
     * @return string
     */
    public function getPolicyStartAt()
    {
        return $this->policyStartAt;
    }

    /**
     * Sets the policy start date
     *
     * @param mixed $policyStartAt
     * @return $this
     */
    public function setPolicyStartAt($policyStartAt)
    {
        $this->policyStartAt = $this->transformDate($policyStartAt);
        return $this;
    }

    /**
     * Gets the full property address excluding postcode
     *
     * @return string
     */
    public function getPropertyAddress()
    {
        $address = $this->propertyAddress1;
        if ($this->propertyAddress2) {
            $address .= ', ' . $this->propertyAddress2;
        }
        if ($this->propertyAddress3) {
            $address .= ', ' . $this->propertyAddress3;
        }
        return $address;
    }

    /**
     * Gets the property address line 1
     *
     * @return string
     */
    public function getPropertyAddress1()
    {
        return $this->propertyAddress1;
    }

    /**
     * Sets the property address line 1
     *
     * @param string $propertyAddress1
     * @return $this
     */
    public function setPropertyAddress1($propertyAddress1)
    {
        $this->propertyAddress1 = $propertyAddress1;
        return $this;
    }

    /**
     * Gets the property address line 2
     *
     * @return string
     */
    public function getPropertyAddress2()
    {
        return $this->propertyAddress2;
    }

    /**
     * Sets the property address line 2
     *
     * @param string $propertyAddress2
     * @return $this
     */
    public function setPropertyAddress2($propertyAddress2)
    {
        $this->propertyAddress2 = $propertyAddress2;
        return $this;
    }

    /**
     * Gets the property address line 3
     *
     * @return string
     */
    public function getPropertyAddress3()
    {
        return $this->propertyAddress3;
    }

    /**
     * Sets the property address line 3
     *
     * @param string $propertyAddress3
     * @return $this
     */
    public function setPropertyAddress3($propertyAddress3)
    {
        $this->propertyAddress2 = $propertyAddress3;
        return $this;
    }

    /**
     * Gets the property postcode
     *
     * @return string
     */
    public function getPropertyPostcode()
    {
        return $this->propertyPostcode;
    }

    /**
     * Sets the property postcode
     *
     * @param string $propertyPostcode
     * @return $this
     */
    public function setPropertyPostcode($propertyPostcode)
    {
        $this->propertyPostcode = $propertyPostcode;
        return $this;
    }

    /**
     * Gets the property rental
     *
     * @return float
     */
    public function getPropertyRental()
    {
        return (float) PolicyOptionsManager::getOption(
            $this->policyOptions,
            self::POLICY_OPTION_RRP,
            $this->amountsCovered
        );
    }

    /**
     * Gets the policy options
     *
     * @return string
     */
    public function getPolicyOptions()
    {
        return $this->policyOptions;
    }

    /**
     * Sets the policy options
     *
     * @param string $policyOptions
     * @return $this
     */
    public function setPolicyOptions($policyOptions)
    {
        $this->policyOptions = $policyOptions;
        return $this;
    }

    /**
     * Gets the cancelled date string
     *
     * @return string
     */
    public function getCancelledDate()
    {
        return $this->cancelledDate;
    }

    /**
     * Sets the cancelled date
     *
     * @param mixed $cancelledDate
     * @return $this
     */
    public function setCancelledDate($cancelledDate)
    {
        $this->cancelledDate = $cancelledDate;
        return $this;
    }

    /**
     * Gets the pay status
     *
     * @return string
     */
    public function getPayStatus()
    {
        return $this->payStatus;
    }

    /**
     * Sets the pay status
     *
     * @param string $payStatus
     * @return $this
     */
    public function setPayStatus($payStatus)
    {
        $this->payStatus = $payStatus;
        return $this;
    }

}